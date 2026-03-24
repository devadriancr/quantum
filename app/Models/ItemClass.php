<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemClass extends Model
{
    protected $fillable = [
        'code',
        'name',
        'status'
    ];
}
