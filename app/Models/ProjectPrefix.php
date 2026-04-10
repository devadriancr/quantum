<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class ProjectPrefix extends Model
{
    use Blameable;

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
