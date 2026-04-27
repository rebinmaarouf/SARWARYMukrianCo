<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Remittance extends Model
{
    protected $fillable = [
        'user_id',
        'account_id',
        'currency_id',
        'amount',
        'type',
        'status',
        'note',
    ];

    /**
     * Get all journal entries for this remittance.
     */
    public function journalEntries(): MorphMany
    {
        return $this->morphMany(JournalEntry::class, 'entryable');
    }
}
