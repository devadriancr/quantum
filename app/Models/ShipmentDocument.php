<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class ShipmentDocument extends Model
{
    use Blameable;

    public const STATUS_OPTIONS = [
        'PENDING'     => 'Pendiente',
        'RECEIVED'    => 'Recibido',
        'PROCESSED'   => 'Procesado',
        'PARTIAL'     => 'Parcial',
        'COMPLETE'    => 'Completo',
        'DISCREPANCY' => 'Discrepancia',
    ];

    protected $fillable = [
        'container_id',
        'partner_id',
        'document_number',
        'document_date',
        'document_time',
        'estimated_arrival_date',
        'estimated_arrival_time',
        'document_status',
        'notes',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    public function container()
    {
        return $this->belongsTo(Container::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function shipmentDocumentLines()
    {
        return $this->hasMany(ShipmentDocumentLine::class, 'shipment_document_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function receptionScans()
    {
        return $this->hasMany(ReceptionScan::class);
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
