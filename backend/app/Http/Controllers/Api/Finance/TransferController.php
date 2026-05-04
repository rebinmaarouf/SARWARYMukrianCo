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
            
            // Commission Revenue Account (IUAS 4x)
            $commissionAccount = \App\Models\Account::where('type', 'revenue')
                ->orWhere('code', 'LIKE', '4%')
                ->first();
            
            $commissionAccountId = $commissionAccount ? $commissionAccount->id : 7; 

            // 1. Record the Transfer object
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

            // 2. Journal Entry - FROM Account (Credit)
            \App\Services\JournalService::record(
                $transfer,
                $validated['from_account_id'],
                $validated['currency_id'],
                0,
                $validated['amount'],
                'حەواڵە بۆ: ' . $transfer->toAccount->name . ($validated['notes'] ? ' - ' . $validated['notes'] : ''),
                now()->format('Y-m-d')
            );

            // 3. Journal Entry - TO Account (Debit)
            \App\Services\JournalService::record(
                $transfer,
                $validated['to_account_id'],
                $validated['currency_id'],
                $validated['amount'],
                0,
                'حەواڵە لە: ' . $transfer->fromAccount->name . ($validated['notes'] ? ' - ' . $validated['notes'] : ''),
                now()->format('Y-m-d')
            );

            // 4. Handle Commission Journal Entries
            if ($commissionAmount > 0) {
                // Deduct commission from Sender (Credit)
                \App\Services\JournalService::record(
                    $transfer,
                    $validated['from_account_id'],
                    $commissionCurrencyId,
                    0,
                    $commissionAmount,
                    'کۆمسیۆنی حەواڵەی #' . $transfer->id,
                    now()->format('Y-m-d')
                );

                // Add to Revenue Account (Debit/Credit depending on revenue type, but we credit income)
                // In accounting, Income is Credited.
                \App\Services\JournalService::record(
                    $transfer,
                    $commissionAccountId,
                    $commissionCurrencyId,
                    0,
                    $commissionAmount,
                    'قازانجی حەواڵەی #' . $transfer->id,
                    now()->format('Y-m-d')
                );
            }

            return response()->json([
                'message' => 'حەواڵەکە بە سەرکەوتوویی ئەنجامدرا',
                'transfer' => $transfer->load(['fromAccount', 'toAccount'])
            ]);
        });
    }

    public function destroy(Transfer $transfer)
    {
        if ($transfer->voided_at) {
            return response()->json(['message' => 'ئەم حەواڵەیە پێشتر پوچەڵ کراوەتەوە'], 422);
        }

        return DB::transaction(function () use ($transfer) {
            $user = auth()->user();

            // 1. Mark as Voided
            $transfer->update([
                'voided_at' => now(),
                'voided_by' => $user->id
            ]);

            // 2. Delete associated Journal Entries (This triggers balance recalculation)
            JournalEntry::where('entryable_id', $transfer->id)
                ->where('entryable_type', Transfer::class)
                ->delete();

            // 3. Soft Delete the Transfer itself
            $transfer->delete();

            return response()->json(['message' => 'حەواڵەکە بە سەرکەوتوویی پوچەڵ کرایەوە و باڵانسەکان ڕاستکرانەوە']);
        });
    }
}
