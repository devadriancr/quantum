<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class InventoryBalance extends Model
{
    use Blameable;

    protected $fillable = [
        'item_id',
        'location_id',
        'opening_quantity',
        'current_quantity',
        'reserved_quantity',
        'last_movement_id',
        'last_movement_date',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    // Se calcula en tiempo real, no se guarda en BD para evitar inconsistencias
    public function getAvailableQuantityAttribute(): float
    {
        return $this->current_quantity - $this->reserved_quantity;
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function lastMovement()
    {
        return $this->belongsTo(StockMovement::class, 'last_movement_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }
}
