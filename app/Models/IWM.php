<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IWM extends Model
{
    protected $connection = 'infor-live';
    protected $table = 'LX834F01.IWM';

    protected $fillable = [
        'LID',
        'LWHS',
        'LDESC'
    ];
}
