<?php

namespace App\Imports;

use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UnitPlansImport implements ToCollection, WithHeadingRow
{
    public array $errors             = [];
    public int   $rows                = 0;
    public int   $skipped             = 0;
    public int   $itemsSkipped        = 0; // items no encontrados en catálogo
    public int   $containersSkipped   = 0; // contenedores con 0 items válidos

    /**
     * Estructura resultante:
     *  groups: [
     *    [
     *      'date'       => 'YYYY-MM-DD',
     *      'date_label' => 'DD/MM/YYYY',
     *      'containers' => [
     *        [
     *          'code'        => 'ONEU0764828',
     *          'items'       => [
     *             ['item_code'=>'PW5T02000','item_id'=>1,'quantity'=>42,'unit_cost'=>1.23,'currency'=>'USD','line_total'=>51.66],
     *             ...
     *          ],
     *          'total_cost'  => 123.45,
     *      ],
     *    ],
     *  ]
     */
    public array $groups = [];

    public function collection(Collection $rows)
    {
        // Normalizar: agrupar por (fecha → contenedor → item)
        $buffer = []; // [date][container][item_code] => qty acumulada

        foreach ($rows as $row) {
            $this->rows++;

            $partNo        = trim((string) ($row['part_no'] ?? ''));
            $qty           = (float) ($row['qty'] ?? 0);
            $containerCode = trim((string) (
                $row['container_no']
                ?? $row['container_no_invoice_no']
                ?? $row['container_number']
                ?? $row['invoice_no']
                ?? ''
            ));
            $customsDate   = $this->parseDate(
                $row['date']
                ?? $row['customs_date']
                ?? $row['customs_date_yyyy_mm_dd']
                ?? null
            );

            if ($partNo === '' || $containerCode === '' || $customsDate === null) {
                $this->skipped++;
                $this->errors[] = "Fila omitida: datos incompletos (parte: '{$partNo}', contenedor: '{$containerCode}').";
                continue;
            }

            $buffer[$customsDate][$containerCode][$partNo] =
                ($buffer[$customsDate][$containerCode][$partNo] ?? 0) + $qty;
        }

        // Resolver items y costos.
        // OJO: array_keys() devuelve int para claves numéricas (PHP convierte
        // automáticamente '73026060' → 73026060). Forzamos string para que el
        // whereIn enlace parámetros nvarchar; de lo contrario SQL Server intenta
        // convertir la columna `code` a int y falla con códigos alfanuméricos.
        $allCodes = collect($buffer)
            ->flatMap(fn($byContainer) => collect($byContainer)->flatMap(fn($byItem) => array_keys($byItem)))
            ->map(fn($code) => (string) $code)
            ->unique()
            ->values();

        // SQL Server limita a 2100 parámetros por consulta. Troceamos el
        // whereIn para soportar planes con miles de códigos distintos.
        $items = $allCodes
            ->chunk(2000)
            ->flatMap(fn($chunk) => Item::whereIn('code', $chunk->values())
                ->with(['lastCost.currency'])
                ->get())
            ->keyBy('code');

        ksort($buffer);

        foreach ($buffer as $date => $byContainer) {
            ksort($byContainer);

            $containers = [];

            foreach ($byContainer as $containerCode => $byItem) {
                $containerTotal = 0.0;
                $linesOut       = [];

                foreach ($byItem as $itemCode => $qty) {
                    $itemCode = (string) $itemCode; // claves numéricas llegan como int
                    $item = $items->get($itemCode);

                    // Filtrar items no registrados en catálogo
                    if (!$item) {
                        $this->itemsSkipped++;
                        continue;
                    }

                    $unitCost = $item->lastCost ? (float) $item->lastCost->total_cost : 0.0;
                    $currency = $item->lastCost?->currency?->code;

                    $lineTotal = round($qty * $unitCost, 4);
                    $containerTotal += $lineTotal;

                    $linesOut[] = [
                        'item_code'  => $itemCode,
                        'item_id'    => $item->id,
                        'quantity'   => $qty,
                        'unit_cost'  => $unitCost,
                        'currency'   => $currency,
                        'line_total' => $lineTotal,
                    ];

                    if (!$item->lastCost) {
                        $this->errors[] = "Item '{$itemCode}' sin costo registrado (contenedor {$containerCode}).";
                    }
                }

                // Si el contenedor quedó sin items válidos, descartarlo
                if (empty($linesOut)) {
                    $this->containersSkipped++;
                    continue;
                }

                $containers[] = [
                    'code'       => $containerCode,
                    'items'      => $linesOut,
                    'total_cost' => round($containerTotal, 4),
                ];
            }

            // Si la fecha quedó sin contenedores, no agregarla al output
            if (empty($containers)) {
                continue;
            }

            $this->groups[] = [
                'date'       => $date,
                'date_label' => Carbon::parse($date)->format('d/m/Y'),
                'containers' => $containers,
            ];
        }
    }

    private function parseDate(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return Carbon::createFromTimestamp(
                    \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp((float) $value)
                )->format('Y-m-d');
            } catch (\Exception) {
                return null;
            }
        }

        $str = trim((string) $value);

        // Soportar formatos comunes
        foreach (['Y-m-d', 'Y/m/d', 'd/m/Y', 'd-m-Y', 'm/d/Y'] as $fmt) {
            try {
                $dt = Carbon::createFromFormat($fmt, $str);
                if ($dt !== false) {
                    return $dt->format('Y-m-d');
                }
            } catch (\Exception) {
                continue;
            }
        }

        try {
            return Carbon::parse($str)->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }
}
