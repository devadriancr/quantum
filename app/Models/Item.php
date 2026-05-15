<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use \App\Models\Traits\Blameable;

    protected $fillable = [
        'code',
        'description',
        'item_class_id',
        'item_type_id',
        'measurement_unit_id',
        'packing_specification_id',
        'default_safety_stock',
        'last_unit_cost',
        'last_unit_cost_currency',
        'active',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    /**
     * Get the item class that owns the item.
     */
    public function itemClass()
    {
        return $this->belongsTo(ItemClass::class);
    }

    /**
     * Get the item type that owns the item.
     */
    public function itemType()
    {
        return $this->belongsTo(ItemType::class);
    }

    /**
     * Get the measurement unit that owns the item.
     */
    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }

    /**
     *  Get the packing specification that owns the item.
     */
    public function packingSpecification()
    {
        return $this->belongsTo(PackingSpecification::class);
    }

    /**
     *  The projects that belong to the item.
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'item_project', 'item_id', 'project_id');
    }

    /**
     * Get the inventory balances for the item.
     */
    public function inventoryBalances()
    {
        return $this->hasMany(InventoryBalance::class);
    }

    /**
     * Get the stock movement lines for the item.
     */
    public function stockMovementLines()
    {
        return $this->hasMany(StockMovementLine::class);
    }

    /**
     * Get the shipment document lines for the item.
     */
    public function shipmentDocumentLines()
    {
        return $this->hasMany(ShipmentDocumentLine::class);
    }

    /**
     * Get the item costs for the item.
     */
    public function itemCosts()
    {
        return $this->hasMany(ItemCost::class);
    }

    /**
     * Get the latest item cost for the item.
     */
    public function lastCost()
    {
        return $this->hasOne(ItemCost::class)->latestOfMany('start_date');
    }
}
