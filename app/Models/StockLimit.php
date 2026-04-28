<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class StockLimit extends Model
{
    use Blameable;

    protected $fillable = [
        'item_id',
        'location_id',
        'minimum_quantity',
        'maximum_quantity',
        'reorder_point',
        'reorder_quantity',
        'active',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
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
