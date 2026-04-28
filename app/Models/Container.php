<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    use Blameable;

    public const TYPE_OPTIONS = [
        'TRUCK'     => 'Camión',
        'CONTAINER' => 'Contenedor',
        'BOX'       => 'Caja',
        'PALLET'    => 'Tarima',
        'OTHER'     => 'Otro',
    ];

    public const STATUS_OPTIONS = [
        'PENDING'    => 'Pendiente',
        'EXPECTED'   => 'Esperado',
        'ARRIVED'    => 'Llegado',
        'UNLOADING'  => 'Descargando',
        'INSPECTION' => 'Inspección',
        'RECEIVED'   => 'Recibido',
        'REJECTED'   => 'Rechazado',
        'IN_TRANSIT' => 'En tránsito',
    ];

    protected $fillable = [
        'code',
        'partner_id',
        'container_type',
        'estimated_arrival_date',
        'estimated_arrival_time',
        'actual_arrival_date',
        'actual_arrival_time',
        'notes',
        'status',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function shipmentDocuments()
    {
        return $this->hasMany(ShipmentDocument::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function canBeDeleted(): bool
    {
        if ($this->status !== 'PENDING') {
            return false;
        }
        if ($this->stockMovements()->exists()) {
            return false;
        }
        return !$this->shipmentDocuments()
            ->whereHas('shipmentDocumentLines', fn($q) => $q->whereNotNull('serial_number'))
            ->exists();
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
