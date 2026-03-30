<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ILM extends Model
{
    protected $connection = 'infor-live';
    protected $table = 'LX834F01.ILM';

    protected $fillable = [
        'WID',
        'WWHS',
        'WLOC',
        'WDESC'
    ];
}
