<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class JournalEntry extends Model
{
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
}
