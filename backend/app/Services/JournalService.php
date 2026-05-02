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
        ?float $rate = null
    ): JournalEntry {
        $date = $date ?? now()->format('Y-m-d');
        
        // 1. Determine the rate
        if ($rate === null) {
            $currency = Currency::find($currencyId);
            if ($currency && $currency->is_base) {
                $rate = 1.0;
            } else {
                // Try to find historical rate, otherwise use current currency rate
                $rate = ExchangeRate::where('currency_id', $currencyId)
                    ->where('date', '<=', $date)
                    ->latest('date')
                    ->value('rate') ?? ($currency->exchange_rate ?? 1.0);
            }
        }

        // 2. Calculate base amount (Value in primary currency)
        $amount = $debit > 0 ? $debit : $credit;
        $baseAmount = $amount * $rate;
        // If credit, make base amount negative for net calculation? 
        // Actually, journal_entries table has base_amount, we should store it as absolute or signed?
        // Usually, it's the value of the line. We can store it as (debit - credit) * rate.
        $signedBaseAmount = ($debit - $credit) * $rate;

        $entry = JournalEntry::create([
            'account_id' => $accountId,
            'currency_id' => $currencyId,
            'debit' => $debit,
            'credit' => $credit,
            'base_amount' => $signedBaseAmount,
            'rate_at_time' => $rate,
            'entryable_id' => $entryable->id,
            'entryable_type' => get_class($entryable),
            'user_id' => auth()->id() ?? 1,
            'date' => $date,
            'description' => $description
        ]);

        return $entry;
    }
}
