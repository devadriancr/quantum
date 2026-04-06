<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeasurementUnit extends Model
{
    use \App\Models\Traits\Blameable;

    protected $fillable = [
        'code',
        'name',
        'status',
        'created_by_user_id',
        'updated_by_user_id',
    ];
}
