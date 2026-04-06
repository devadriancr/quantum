<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use \App\Models\Traits\Blameable;

    protected $fillable = [
        'code',
        'name',
        'row',
        'rack',
        'shelf',
        'zone',
        'available_capacity',
        'status',
        'warehouse_id',
        'created_by_user_id',
        'updated_by_user_id'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
