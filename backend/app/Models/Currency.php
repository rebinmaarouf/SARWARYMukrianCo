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
        
        // Return latest rate from exchange_rates table, or fallback to 1
        $latest = $this->exchangeRates()->latest('date')->first();
        return $latest ? (float)$latest->rate : 1.0;
    }
}
