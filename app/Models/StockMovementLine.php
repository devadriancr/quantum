<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class StockMovementLine extends Model
{
    use Blameable;

    protected $fillable = [
        'stock_movement_id',
        'shipment_document_line_id',
        'item_id',
        'quantity_received',
        'unit_cost',
        'serial_batch_number',
        'notes',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    public function stockMovement()
    {
        return $this->belongsTo(StockMovement::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function shipmentDocumentLine()
    {
        return $this->belongsTo(ShipmentDocumentLine::class);
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
