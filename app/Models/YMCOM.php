<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class YMCOM extends Model
{
    protected $connection = 'infor-live';
    protected $table = 'LX834FU01.YMCOM';

    protected $fillable = [
        'MCFPRO',
        'MCFCLS',
        'MCCPRO',
        'MCCCLS',
        'MCQREQ',
        'MCCCTM',
        'MCCUSR',
        'MCCCDT',
    ];

    /**
     * Retorna los hijos directos de un número de parte padre con la cantidad
     * ya multiplicada por el forecast. Si se pasa $class, filtra por MCCCLS;
     * si es null, regresa hijos de todas las clases.
     */
    public static function getChildren(
        string  $parentPartNumber,
        float   $requiredQuantity,
        string  $requiredDate,
        ?string $childClass = null
    ): Collection {
        $parentPartNumber = trim($parentPartNumber);

        $cacheKey = $childClass
            ? "ymcom_children_{$parentPartNumber}_{$childClass}"
            : "ymcom_children_{$parentPartNumber}_all";

        $children = cache()->remember(
            $cacheKey,
            now()->addMinutes(30),
            function () use ($parentPartNumber, $childClass) {
                $query = self::whereRaw('TRIM(MCFPRO) = ?', [$parentPartNumber])
                    ->whereRaw('TRIM(MCFPRO) != TRIM(MCCPRO)');

                if ($childClass !== null) {
                    $query->where('MCCCLS', $childClass);
                }

                return $query->get();
            }
        );

        return $children->map(fn ($child) => [
            'part_number'        => trim($child->MCCPRO),
            'parent_part_number' => $parentPartNumber,
            'required_quantity'  => $child->MCQREQ * $requiredQuantity,
            'required_date'      => $requiredDate,
        ]);
    }
}
