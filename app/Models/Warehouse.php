<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use Blameable;

    protected $fillable = [
        'code',
        'name',
        'total_capacity',
        'status',
        'created_by_user_id',
        'updated_by_user_id'
    ];

    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}
