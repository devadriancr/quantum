<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class UnitPlan extends Model
{
    use Blameable;

    protected $fillable = [
        'name',
        'week_start_date',
        'total_cost',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    protected $casts = [
        'week_start_date' => 'date',
        'total_cost'      => 'decimal:4',
    ];

    public function lines()
    {
        return $this->hasMany(UnitPlanLine::class);
    }
}
