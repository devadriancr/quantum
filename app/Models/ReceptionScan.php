<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class ReceptionScan extends Model
{

    use Blameable;

    protected $fillable = [
        'stock_movement_id',
        'shipment_document_id',
        'scan_content',
        'consignment_type',
        'parsed_serial',
        'parsed_item_code',
        'parsed_supplier',
        'parsed_quantity',
        'scan_status',
        'error_message',
        'matched_document_line_id',
        'stock_movement_line_id',
        'created_by_user_id',
    ];

    public function stockMovement()
    {
        return $this->belongsTo(StockMovement::class);
    }

    public function shipmentDocument()
    {
        return $this->belongsTo(ShipmentDocument::class);
    }

    public function matchedDocumentLine()
    {
        return $this->belongsTo(ShipmentDocumentLine::class, 'matched_document_line_id');
    }

    public function stockMovementLine()
    {
        return $this->belongsTo(StockMovementLine::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
} 
