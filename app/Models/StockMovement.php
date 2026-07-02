<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use Blameable;

    public const MOVEMENT_TYPES = [
        'INBOUND'    => 'Entrada',
        'OUTBOUND'   => 'Salida',
        'ADJUSTMENT' => 'Ajuste',
        'TRANSFER'   => 'Transferencia',
        'RETURN'     => 'Retorno',
    ];

    public const STATUS_OPTIONS = [
        'PENDING'    => 'Pendiente',
        'IN_TRANSIT' => 'En Tránsito',
        'COMPLETED'  => 'Completado',
        'CANCELED'   => 'Cancelado',
        'RECEIVED'   => 'Recibido',
        'VERIFIED'   => 'Verificado',
        'RECORDED'   => 'Registrado',
        'REJECTED'   => 'Rechazado',
    ];

    protected $fillable = [
        'movement_number',
        'movement_date',
        'movement_time',
        'transaction_type_id',
        'movement_type',
        'location_id_from',
        'location_id_to',
        'partner_id',
        'container_id',
        'shipment_document_id',
        'invoice_number',
        'carta_porte',
        'notes',
        'status',
        'verified_by_user_id',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    public function transactionType()
    {
        return $this->belongsTo(TransactionType::class);
    }

    public function locationFrom()
    {
        return $this->belongsTo(Location::class, 'location_id_from');
    }

    public function locationTo()
    {
        return $this->belongsTo(Location::class, 'location_id_to');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function container()
    {
        return $this->belongsTo(Container::class);
    }

    public function lines()
    {
        return $this->hasMany(StockMovementLine::class);
    }

    public function inventoryBalances()
    {
        return $this->hasMany(InventoryBalance::class, 'last_movement_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public function shipmentDocument()
    {
        return $this->belongsTo(ShipmentDocument::class);
    }

    public function receptionScans()
    {
        return $this->hasMany(ReceptionScan::class);
    }
}
