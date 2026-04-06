<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use \App\Models\Traits\Blameable;

    protected $fillable = [
        'code', 'name', 'partner_type', 'contact_email', 'contact_phone', 'address', 'city', 'country', 'status', 'created_by_user_id', 'updated_by_user_id'
    ];
}
