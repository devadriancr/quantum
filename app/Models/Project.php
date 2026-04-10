<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use Blameable;

    protected $fillable = [
        'code',
        'name',
        'model',
        'description',
        'partner_id',
        'status',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function prefixes()
    {
        return $this->hasMany(ProjectPrefix::class);
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_project', 'project_id', 'item_id');
    }
}
