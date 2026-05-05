<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;

use App\Traits\BelongsToBranch;

class Transaction extends Model
{
    use SoftDeletes, BelongsToBranch;

    protected $fillable = [
        'user_id',
        'account_id',
        'type',
        'pair',
        'primary_currency',
        'primary_amount',
        'secondary_currency',
        'secondary_amount',
        'rate',
        'profit',
        'client_name',
        'note',
        'vault_from_id',
        'vault_to_id',
        'branch_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function vault_from()
    {
        return $this->belongsTo(Account::class, 'vault_from_id');
    }

    public function vault_to()
    {
        return $this->belongsTo(Account::class, 'vault_to_id');
    }

    /**
     * Get all journal entries for this transaction.
     */
    public function journalEntries(): MorphMany
    {
        return $this->morphMany(JournalEntry::class, 'entryable');
    }

    protected static function booted()
    {
        static::deleted(function ($transaction) {
            $transaction->journalEntries()->delete();
        });
    }
}
