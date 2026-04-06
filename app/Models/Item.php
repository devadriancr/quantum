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
        'active',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    /**
     *
     */
    public function itemClass()
    {
        return $this->belongsTo(ItemClass::class);
    }

    /**
     *
     */
    public function itemType()
    {
        return $this->belongsTo(ItemType::class);
    }

    /**
     *
     */
    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }

    /**
     *
     */
    public function packingSpecification()
    {
        return $this->belongsTo(PackingSpecification::class);
    }

    /**
     *
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'item_project', 'item_id', 'project_id');
    }
}
