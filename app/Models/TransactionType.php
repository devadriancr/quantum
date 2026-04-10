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
        'status'
    ];
}
