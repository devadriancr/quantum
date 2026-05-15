<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YCS2 extends Model
{
    protected $connection = 'infor-live';
    protected $table = 'LX834FU01.YCS2';

    protected $fillable = [
        'Y2ITEM',
        'Y2VEND',
        'Y2CURR',
        'Y2SDTE',
        'Y2EDTE',
        'Y2CSTT'
    ];
}
