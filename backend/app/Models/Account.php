<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'code',
        'name',
        'mobile',
        'address',
        'type',
        'balance',
    ];

    /**
     * Scope: Search by code or name (ultra-fast lookup).
     * User types '13' → finds code 13.
     * User types 'نات' → finds 'کۆمپانیای ناترۆن'.
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('code', 'LIKE', "{$term}%")
              ->orWhere('name', 'LIKE', "%{$term}%");
        });
    }

    /**
     * Get all registry entries where this account is debtor.
     */
    public function debtorEntries()
    {
        return $this->hasMany(RegistryEntry::class, 'debtor_account_id');
    }

    /**
     * Get all registry entries where this account is creditor.
     */
    public function creditorEntries()
    {
        return $this->hasMany(RegistryEntry::class, 'creditor_account_id');
    }
}
