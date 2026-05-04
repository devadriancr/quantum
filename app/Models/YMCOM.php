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
     * Obtiene recursivamente los hijos de un número de parte padre.
     *
     * @param string     $parentPartNumber  Número de parte padre
     * @param float      $requiredQuantity  Cantidad requerida acumulada
     * @param string     $requiredDate      Fecha requerida (del forecast)
     * @param array      $processed         Control de ciclos (uso interno)
     * @return Collection
     */
    public static function getChildren(
        string $parentPartNumber,
        float $requiredQuantity,
        string $requiredDate,
        array $processed = []
    ): Collection {
        // Evitar ciclos infinitos
        if (in_array($parentPartNumber, $processed)) {
            return collect();
        }

        $processed[] = $parentPartNumber;

        // Cache por número de parte para no repetir queries a Infor
        $children = cache()->remember(
            "ymcom_children_{$parentPartNumber}",
            now()->addMinutes(30),
            fn () => self::whereRaw('TRIM(MCFPRO) = ?', [trim($parentPartNumber)])->get()
        );

        $allChildren = collect();

        foreach ($children as $child) {
            $childPart  = trim($child->MCCPRO);
            $parentPart = trim($child->MCFPRO);

            // Ignorar auto-referencias y filtrar solo clase S1
            if ($childPart === $parentPart || $child->MCCCLS !== 'S1') {
                continue;
            }

            $allChildren->push([
                'part_number'       => $childPart,
                'parent_part_number'=> $parentPart,
                'required_quantity' => $child->MCQREQ * $requiredQuantity,
                'required_date'     => $requiredDate,
            ]);

            // Recursión para sub-hijos
            $allChildren = $allChildren->merge(
                self::getChildren($childPart, $child->MCQREQ * $requiredQuantity, $requiredDate, $processed)
            );
        }

        return $allChildren;
    }
}
