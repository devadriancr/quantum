<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackingSpecification extends Model
{
    use \App\Models\Traits\Blameable;

    protected $fillable = [
        'name',
        'quantity',
        'status',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    /**
     *
     */
    public function items(){
        return $this->hasMany(Item::class);
    }
}
