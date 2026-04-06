<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPrefix extends Model
{
    use \App\Models\Traits\Blameable;

    protected $fillable = [
        'code',
        'project_id',
        'description',
        'status',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
