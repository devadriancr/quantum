<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitPlanLine extends Model
{
    protected $fillable = [
        'unit_plan_id',
        'container_code',
        'customs_date',
        'item_id',
        'item_code',
        'quantity',
        'day_of_week',
        'slot_index',
        'schedule_time',
        'unit_cost',
        'line_total',
        'currency_id',
    ];

    protected $casts = [
        'customs_date' => 'date',
        'quantity'     => 'decimal:2',
        'unit_cost'    => 'decimal:4',
        'line_total'   => 'decimal:4',
    ];

    public function unitPlan()
    {
        return $this->belongsTo(UnitPlan::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
