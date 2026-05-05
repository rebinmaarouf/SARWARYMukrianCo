<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Traits\HasAudit;

use App\Traits\BelongsToBranch;

class RegistryEntry extends Model
{
    use SoftDeletes, HasAudit, BelongsToBranch;

    protected $fillable = [
        'entry_date',
        'currency_id',
        'amount',
        'debtor_account_id',
        'creditor_account_id',
        'commission_1',
        'commission_2',
        'sender',
        'receiver',
        'notes',
        'user_id',
        'branch_id',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'commission_1' => 'decimal:4',
        'commission_2' => 'decimal:4',
        'entry_date' => 'date',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function debtorAccount()
    {
        return $this->belongsTo(Account::class, 'debtor_account_id');
    }

    public function creditorAccount()
    {
        return $this->belongsTo(Account::class, 'creditor_account_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all journal entries for this registry entry.
     */
    public function journalEntries(): MorphMany
    {
        return $this->morphMany(JournalEntry::class, 'entryable');
    }

    protected static function booted()
    {
        static::deleted(function ($entry) {
            // Cascade delete journal entries
            $entry->journalEntries()->delete();
        });
    }
}
