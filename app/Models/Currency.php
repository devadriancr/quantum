<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'units_per_usd',
        'active',
    ];

    protected $casts = [
        'units_per_usd' => 'decimal:6',
        'active'        => 'boolean',
    ];

    public function itemCosts()
    {
        return $this->hasMany(ItemCost::class);
    }

    /**
     * Convert an amount from this currency to another currency using their exchange rates.
     */
    public function convert(float $amount, self $toCurrency): float
    {
        return ($amount / $this->units_per_usd) * $toCurrency->units_per_usd;
    }

    /**
     * Convert an amount from one currency to another using their exchange rates.
     */
    public static function exchange(float $amount, string $from, string $to): float
    {
        $fromCurrency = Currency::whereCode($from)->firstOrFail();
        $toCurrency   = Currency::whereCode($to)->firstOrFail();

        return $fromCurrency->convert($amount, $toCurrency);
    }
}
