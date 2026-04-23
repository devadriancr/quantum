<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class TransactionType extends Model
{
    use Blameable;

    protected $fillable = [
        'code',
        'description',
        'transaction_category',
        'affects_inventory',
        'direction',
        'status',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
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
