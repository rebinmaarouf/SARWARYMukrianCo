<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\HasAudit;

class Transfer extends Model
{
    use SoftDeletes, HasAudit;

    protected $fillable = [
        'from_account_id',
        'to_account_id',
        'currency_id',
        'amount',
        'commission_amount',
        'commission_currency_id',
        'commission_account_id',
        'notes',
        'user_id',
        'voided_at',
        'voided_by'
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
