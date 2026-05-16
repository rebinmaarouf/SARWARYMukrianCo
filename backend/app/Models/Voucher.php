<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToBranch;

class Voucher extends Model
{
    use HasFactory, SoftDeletes, BelongsToBranch;

    protected $fillable = [
        'voucher_number',
        'type',
        'amount',
        'currency_id',
        'account_id',
        'vault_id',
        'branch_id',
        'user_id',
        'date',
        'due_date',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'amount' => 'decimal:4'
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function vault()
    {
        return $this->belongsTo(Account::class, 'vault_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function journalEntries()
    {
        return $this->morphMany(JournalEntry::class, 'entryable');
    }
}
