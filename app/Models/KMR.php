<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KMR extends Model
{
    protected $connection = 'infor-live';
    protected $table = 'LX834F01.KMR';

    protected $fillable = [
        'MID',
        'MPROD',
        'MRDTE',
        'MTYPE',
        'MSEQ',
        'MPRNT',
        'MQTY',
        'MPDTE',
        'MRWHS',
        'MLOT',
        'MFRWH',
        'MRSYS',
        'MRCBP',
        'MRUSC',
        'MRRGEN',
        'MRRTME',
        'MRRTZ',
        'MRPTME',
        'MRPTZ',
        'MRCBY',
        'MRCDT',
        'MRCTM',
        'MRCTZ',
        'MRLBY',
        'MRLDT',
        'MRLTM',
        'MRLTZ',
        'MRLPGM',
        'MRFAC',
        'MRUDF1',
        'MRUDF2',
        'MRCSEQ',
        'MRCNO',
        'MRTCNO',
        'MRPBOM',
        'MRPRGM',

    ];
}
