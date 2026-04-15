<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use Blameable;

    public const PARTNER_TYPES = [
        'CUSTOMER' => 'Cliente',
        'SUPPLIER' => 'Proveedor',
        'BOTH' => 'Ambos',
    ];

    protected $fillable = [
        'code',
        'name',
        'partner_type',
        'contact_email',
        'contact_phone',
        'address',
        'city',
        'country',
        'status',
        'created_by_user_id',
        'updated_by_user_id'
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
