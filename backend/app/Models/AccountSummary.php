<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountSummary extends Model
{
    protected $fillable = [
        'account_id',
        'currency_id',
        'total_debit',
        'total_credit',
    ];

    protected $casts = [
        'total_debit' => 'decimal:4',
        'total_credit' => 'decimal:4',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function getBalanceAttribute()
    {
        return $this->total_debit - $this->total_credit;
    }
}
