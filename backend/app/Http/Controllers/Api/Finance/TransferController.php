<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\Transfer;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function index()
    {
        return Transfer::with(['fromAccount', 'toAccount', 'currency'])
            ->latest()
            ->paginate(50);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'required|exists:accounts,id|different:from_account_id',
            'currency_id' => 'required|exists:currencies,id',
            'amount' => 'required|numeric|min:0.01',
            'commission_amount' => 'nullable|numeric|min:0',
            'commission_currency_id' => 'nullable|exists:currencies,id',
            'notes' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $commissionAmount = $request->input('commission_amount', 0);
            $commissionCurrencyId = $request->input('commission_currency_id', $validated['currency_id']);
            $commissionAccountId = 7; // Main Commission Revenue Account ID

            // 1. Record the Transfer
            $transfer = Transfer::create([
                'from_account_id' => $validated['from_account_id'],
                'to_account_id' => $validated['to_account_id'],
                'currency_id' => $validated['currency_id'],
                'amount' => $validated['amount'],
                'commission_amount' => $commissionAmount,
                'commission_currency_id' => $commissionCurrencyId,
                'commission_account_id' => $commissionAccountId,
                'notes' => $validated['notes'] ?? null,
                'user_id' => $request->user()->id,
            ]);

            // 2. Journal Entry - FROM (Credit - Money leaving for principal)
            JournalEntry::create([
                'account_id' => $validated['from_account_id'],
                'currency_id' => $validated['currency_id'],
                'debit' => 0,
                'credit' => $validated['amount'],
                'type' => 'transfer',
                'description' => 'Transfer To: ' . $transfer->toAccount->name . ($validated['notes'] ? ' - ' . $validated['notes'] : ''),
                'user_id' => $request->user()->id,
                'reference_id' => $transfer->id,
                'reference_type' => Transfer::class,
                'date' => now(),
            ]);

            // 3. Journal Entry - TO (Debit - Money entering for principal)
            JournalEntry::create([
                'account_id' => $validated['to_account_id'],
                'currency_id' => $validated['currency_id'],
                'debit' => $validated['amount'],
                'credit' => 0,
                'type' => 'transfer',
                'description' => 'Transfer From: ' . $transfer->fromAccount->name . ($validated['notes'] ? ' - ' . $validated['notes'] : ''),
                'user_id' => $request->user()->id,
                'reference_id' => $transfer->id,
                'reference_type' => Transfer::class,
                'date' => now(),
            ]);

            // 4. Handle Commission Journal Entries
            if ($commissionAmount > 0) {
                // Deduct commission from Sender (Credit)
                JournalEntry::create([
                    'account_id' => $validated['from_account_id'],
                    'currency_id' => $commissionCurrencyId,
                    'debit' => 0,
                    'credit' => $commissionAmount,
                    'type' => 'commission',
                    'description' => 'Commission Fee for Transfer #' . $transfer->id,
                    'user_id' => $request->user()->id,
                    'reference_id' => $transfer->id,
                    'reference_type' => Transfer::class,
                    'date' => now(),
                ]);

                // Add to Revenue Account (Debit, since Balance = Debit - Credit)
                JournalEntry::create([
                    'account_id' => $commissionAccountId,
                    'currency_id' => $commissionCurrencyId,
                    'debit' => $commissionAmount,
                    'credit' => 0,
                    'type' => 'commission',
                    'description' => 'Commission Revenue from Transfer #' . $transfer->id,
                    'user_id' => $request->user()->id,
                    'reference_id' => $transfer->id,
                    'reference_type' => Transfer::class,
                    'date' => now(),
                ]);

                // Update summaries for commission
                $this->updateSummary($validated['from_account_id'], $commissionCurrencyId, -$commissionAmount);
                $this->updateSummary($commissionAccountId, $commissionCurrencyId, $commissionAmount);
            }

            // 5. Update Summaries for principal
            $this->updateSummary($validated['from_account_id'], $validated['currency_id'], -$validated['amount']);
            $this->updateSummary($validated['to_account_id'], $validated['currency_id'], $validated['amount']);

            return response()->json([
                'message' => 'Transfer completed successfully',
                'transfer' => $transfer->load(['fromAccount', 'toAccount'])
            ]);
        });
    }

    private function updateSummary($accountId, $currencyId, $amount)
    {
        $summary = \App\Models\AccountSummary::firstOrNew([
            'account_id' => $accountId,
            'currency_id' => $currencyId,
        ]);
        
        $summary->balance = ($summary->balance ?? 0) + $amount;
        $summary->save();
    }
}
