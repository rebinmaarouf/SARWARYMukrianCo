<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'name',
        'code',
        'symbol',
        'is_base',
    ];

    protected $casts = [
        'is_base' => 'boolean',
    ];

    protected $appends = ['current_rate'];

    public function exchangeRates()
    {
        return $this->hasMany(ExchangeRate::class);
    }

    public function getCurrentRateAttribute()
    {
        if ($this->is_base) return 1;
        
        // Return latest rate from exchange_rates table, or fallback to exchange_rate_to_base, or 1.0
        $latest = $this->exchangeRates()->latest('date')->first();
        if ($latest) {
            return (float)$latest->rate;
        }
        
        return (float)($this->exchange_rate_to_base ?? 1.0);
    }
}
