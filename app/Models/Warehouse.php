<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = [
        'code',
        'name',
        'total_capacity',
        'status'
    ];

    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}
