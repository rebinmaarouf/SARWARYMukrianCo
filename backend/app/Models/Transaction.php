<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Transaction extends Model
{
    use SoftDeletes;

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
        'note'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get all journal entries for this transaction.
     */
    public function journalEntries(): MorphMany
    {
        return $this->morphMany(JournalEntry::class, 'entryable');
    }
}
