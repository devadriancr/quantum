<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemCost extends Model
{
    protected $fillable = [
        'item_id',
        'currency_id',
        'vendor_number',
        'start_date',
        'end_date',
        'total_cost',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'total_cost' => 'decimal:4',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
