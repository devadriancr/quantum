<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UnitPlanExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
     * @param array $rows Filas ya formateadas en el orden de headings().
     */
    public function __construct(private array $rows) {}

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'DÍA',
            'FECHA ENTREGA',
            'HORARIO',
            'FECHA ADUANA',
            'CONTENEDOR',
            'N° PARTE',
            'CANTIDAD',
            'COSTO UNITARIO',
            'TOTAL',
            'MONEDA',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // N° de parte como texto para no perder ceros a la izquierda.
        $sheet->getStyle('F')
              ->getNumberFormat()
              ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
