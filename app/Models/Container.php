<?php

namespace App\Models;

use App\Models\ShipmentDocument;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    use HasFactory;

    public const TYPE_OPTIONS = [
        'TRUCK' => 'Camión',
        'CONTAINER' => 'Contenedor',
        'BOX' => 'Caja',
        'PALLET' => 'Tarima',
        'OTHER' => 'Otro',
    ];

    public const STATUS_OPTIONS = [
        'PENDING' => 'Pendiente',
        'EXPECTED' => 'Esperado',
        'ARRIVED' => 'Llegado',
        'UNLOADING' => 'Descargando',
        'INSPECTION' => 'Inspección',
        'RECEIVED' => 'Recibido',
        'REJECTED' => 'Rechazado',
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
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function shipmentDocuments()
    {
        return $this->hasMany(ShipmentDocument::class);
    }
}
