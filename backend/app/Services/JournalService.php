<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class JournalService
{
    /**
     * Record a journal entry and return it.
     */
    public static function record(
        Model $entryable,
        int $accountId,
        int $currencyId,
        float $debit = 0,
        float $credit = 0,
        ?string $description = null,
        ?string $date = null,
        ?float $rate = null,
        ?int $branchId = null
    ): JournalEntry {
        $date = $date ?? now()->format('Y-m-d');
        
        // Check balance for Vault accounts
        $account = \App\Models\Account::find($accountId);
        if ($account && $account->type === 'vault') {
            $currentBalance = $account->getBalance($currencyId);
            // Vault is an Asset: Debit increases (+), Credit decreases (-)
            $newBalance = $currentBalance + $debit - $credit;
            if ($newBalance < 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'balance' => ['باڵانسی سندوقەکە بەشی ئەم مامەڵەیە ناکات! باڵانسی ئێستا: ' . number_format($currentBalance, 2)]
                ]);
            }
        }

        // 1. Determine the rate
        if ($rate === null) {
            $currency = Currency::find($currencyId);
            if ($currency && $currency->is_base) {
                $rate = 1.0;
            } else {
                $rate = ExchangeRate::where('currency_id', $currencyId)
                    ->where('date', '<=', $date)
                    ->latest('date')
                    ->value('rate') ?? ($currency->exchange_rate ?? 1.0);
            }
        }

        // 2. Calculate base amount
        $signedBaseAmount = ($debit - $credit) * $rate;

        // 3. Determine Branch
        if (!$branchId) {
            $branchId = auth()->user()?->branch_id ?? \App\Models\Account::find($accountId)?->branch_id ?? 1;
        }

        return JournalEntry::create([
            'account_id' => $accountId,
            'currency_id' => $currencyId,
            'debit' => $debit,
            'credit' => $credit,
            'base_amount' => $signedBaseAmount,
            'rate_at_time' => $rate,
            'entryable_id' => $entryable->id,
            'entryable_type' => get_class($entryable),
            'user_id' => auth()->id() ?? 1,
            'branch_id' => $branchId,
            'date' => $date,
            'description' => $description
        ]);
    }
}
