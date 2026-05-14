<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class InventoryAdjustment extends Model
{
    use Blameable;

    public const ADJUSTMENT_TYPES = [
        'VARIANCE'   => 'Varianza de conteo',
        'DAMAGE'     => 'Daño / Merma',
        'LOSS'       => 'Pérdida',
        'FOUND'      => 'Material encontrado',
        'CORRECTION' => 'Corrección de registro',
    ];

    public const STATUS_OPTIONS = [
        'DRAFT'   => 'Borrador',
        'APPLIED' => 'Aplicado',
    ];

    protected $fillable = [
        'adjustment_number',
        'adjustment_date',
        'adjustment_type',
        'reason',
        'status',
        'notes',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
    ];

    public function lines()
    {
        return $this->hasMany(InventoryAdjustmentLine::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }
}
