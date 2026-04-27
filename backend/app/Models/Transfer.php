<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Account;

class Transfer extends Model
{
    protected $fillable = [
        'from_account_id',
        'to_account_id',
        'currency_id',
        'amount',
        'notes',
        'user_id'
    ];

    public function fromAccount()
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
