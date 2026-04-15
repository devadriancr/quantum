<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentDocumentLine extends Model
{
    public const STATUS_OPTIONS = [
        'PENDING' => 'Pendiente',
        'RECEIVED' => 'Recibido',
        'DAMAGED' => 'Dañado',
        'EXPECTED' => 'Esperado',
        'DISCREPANCY' => 'Discrepancia',
    ];

    protected $fillable = [
        'shipment_document_id',
        'line_number',
        'item_id',
        'serial_number',
        'quantity_received',
        'status'
    ];

    public function shippingDocument()
    {
        return $this->belongsTo(ShipmentDocument::class, 'shipment_document_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
