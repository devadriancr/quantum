<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class ShipmentDocumentLine extends Model
{
    use Blameable;

    public const STATUS_OPTIONS = [
        'PENDING'     => 'Pendiente',
        'RECEIVED'    => 'Recibido',
        'DAMAGED'     => 'Dañado',
        'EXPECTED'    => 'Esperado',
        'DISCREPANCY' => 'Discrepancia',
    ];

    protected $fillable = [
        'shipment_document_id',
        'line_number',
        'item_id',
        'serial_number',
        'quantity_declared',
        'quantity_received',
        'unit_cost',
        'status',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    public function shipmentDocument()
    {
        return $this->belongsTo(ShipmentDocument::class, 'shipment_document_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function stockMovementLines()
    {
        return $this->hasMany(StockMovementLine::class, 'shipment_document_line_id');
    }

    public function receptionScans()
    {
        return $this->hasMany(ReceptionScan::class, 'matched_document_line_id');
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
