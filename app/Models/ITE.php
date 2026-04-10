<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ITE extends Model
{
    protected $connection = 'infor-live';
    protected $table = 'LX834F01.ITE';

    protected $fillable = [
        'TID',
        'TTYPE',
        'TDESC'
    ];
}
