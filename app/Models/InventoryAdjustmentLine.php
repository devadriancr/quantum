<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryAdjustmentLine extends Model
{
    protected $fillable = [
        'inventory_adjustment_id',
        'line_number',
        'item_id',
        'location_id',
        'system_quantity',
        'adjusted_quantity',
        'notes',
    ];

    protected $casts = [
        'system_quantity'   => 'decimal:2',
        'adjusted_quantity' => 'decimal:2',
        'variance'          => 'decimal:2',
    ];

    public function adjustment()
    {
        return $this->belongsTo(InventoryAdjustment::class, 'inventory_adjustment_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
