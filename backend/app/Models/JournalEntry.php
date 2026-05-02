<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Traits\HasAudit;

class JournalEntry extends Model
{
    use SoftDeletes, HasAudit;

    protected $fillable = [
        'account_id',
        'currency_id',
        'debit',
        'credit',
        'base_amount',
        'rate_at_time',
        'entryable_id',
        'entryable_type',
        'user_id',
        'date',
        'description',
        'reference_id',
        'reference_type',
        'type'
    ];

    protected $casts = [
        'date' => 'date',
        'debit' => 'decimal:4',
        'credit' => 'decimal:4',
        'base_amount' => 'decimal:4',
        'rate_at_time' => 'decimal:4',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entryable(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function booted()
    {
        static::saved(function ($entry) {
            static::recalculateFor($entry->account_id, $entry->currency_id);
        });

        static::deleted(function ($entry) {
            static::recalculateFor($entry->account_id, $entry->currency_id);
        });
    }

    public static function recalculateFor($accountId, $currencyId)
    {
        $totals = static::where('account_id', $accountId)
            ->where('currency_id', $currencyId)
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();

        AccountSummary::updateOrCreate(
            ['account_id' => $accountId, 'currency_id' => $currencyId],
            [
                'total_debit' => $totals->total_debit ?? 0,
                'total_credit' => $totals->total_credit ?? 0
            ]
        );
    }
}
