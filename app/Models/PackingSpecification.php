<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackingSpecification extends Model
{
    protected $fillable = [
        'name',
        'quantity',
        'status'
    ];
}
