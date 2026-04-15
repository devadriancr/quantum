<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentDocument extends Model
{
    public const STATUS_OPTIONS = [
        'PENDING' => 'Pendiente',
        'RECEIVED' => 'Recibido',
        'PROCESSED' => 'Procesado',
        'PARTIAL' => 'Parcial',
        'COMPLETE' => 'Completo',
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
    ];

    public function container()
    {
        return $this->belongsTo(Container::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function lines()
    {
        return $this->hasMany(ShipmentDocumentLine::class, 'shipment_document_id');
    }
}
