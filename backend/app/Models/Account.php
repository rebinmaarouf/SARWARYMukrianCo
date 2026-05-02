<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasAudit;

class Account extends Model
{
    use SoftDeletes, HasAudit;

    protected $fillable = [
        'code',
        'name',
        'mobile',
        'address',
        'type',
        'balance',
        'parent_id',
    ];

    /**
     * Hierarchy Relations
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    /**
     * Accounting Relations
     */
    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function summaries(): HasMany
    {
        return $this->hasMany(AccountSummary::class);
    }

    /**
     * Get balance for a specific currency from the summary table.
     */
    public function getBalance($currencyId)
    {
        $summary = $this->summaries()->where('currency_id', $currencyId)->first();
        return $summary ? $summary->balance : 0;
    }

    /**
     * Scope: Search by code or name (ultra-fast lookup).
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            // Priority: Start match for code, partial match for name
            $q->where('code', 'LIKE', "{$term}%")
              ->orWhere('name', 'LIKE', "%{$term}%")
              ->orWhere('mobile', 'LIKE', "{$term}%");
        })->orderByRaw("CASE 
            WHEN code = ? THEN 1 
            WHEN code LIKE ? THEN 2 
            WHEN name LIKE ? THEN 3 
            ELSE 4 END", [$term, "{$term}%", "{$term}%"]);
    }

    /**
     * Legacy Relations
     */
    public function debtorEntries()
    {
        return $this->hasMany(RegistryEntry::class, 'debtor_account_id');
    }

    public function creditorEntries()
    {
        return $this->hasMany(RegistryEntry::class, 'creditor_account_id');
    }
}
