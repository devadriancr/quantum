<?php

namespace App\Imports;

use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class UnitPlansImport
{
    /**
     * Estructura resultante de la pestaña "Container":
     *  groups: [
     *    [
     *      'date'       => 'YYYY-MM-DD',
     *      'date_label' => 'DD-MM-YYYY',
     *      'containers' => [
     *        [
     *          'code'       => 'ONEU0764828',
     *          'items'      => [
     *             ['item_code'=>'PW5T02000','item_id'=>1,'quantity'=>42,'unit_cost'=>1.23,'currency'=>'USD','line_total'=>51.66],
     *             ...
     *          ],
     *          'total_cost' => 123.45,
     *        ],
     *      ],
     *    ],
     *  ]
     */
    public array $groups            = [];

    /**
     * Items de la pestaña "PICS": [item_code => total_qty]
     * Solo contiene items que existen en el catálogo.
     */
    public array $picsItems         = [];

    public array $errors            = [];
    public int   $rows              = 0;
    public int   $skipped           = 0;
    public int   $itemsSkipped      = 0;
    public int   $containersSkipped = 0;

    /**
     * Punto de entrada: carga el archivo y procesa ambas pestañas.
     * Se requiere la extensión original para seleccionar el reader correcto,
     * ya que el archivo temporal de Livewire no siempre tiene extensión reconocible.
     *
     * XLSB: formato binario no soportado por PhpSpreadsheet. Se convierte
     * automáticamente a XLSX vía Excel COM si está disponible en Windows.
     */
    public function import(string $filePath, string $extension = 'xlsx'): void
    {
        $ext = strtolower($extension);

        if ($ext === 'xlsb') {
            $tempXlsx = $this->convertXlsbToXlsx($filePath);

            if ($tempXlsx === null) {
                throw new \RuntimeException(
                    "El formato .xlsb no se puede procesar directamente.\n" .
                    "Abre el archivo en Excel, ve a Guardar como y selecciona el formato .xlsx, " .
                    "luego vuelve a cargarlo."
                );
            }

            try {
                $reader = IOFactory::createReader('Xlsx');
                $reader->setReadDataOnly(true);
                $reader->setLoadSheetsOnly(['Container', 'PICS']);
                $spreadsheet = $reader->load($tempXlsx);
                $this->processContainerSheet($spreadsheet);
                $this->processPicsSheet($spreadsheet);
            } finally {
                @unlink($tempXlsx);
            }

            return;
        }

        $readerType = match ($ext) {
            'xls'   => 'Xls',
            'csv'   => 'Csv',
            default => 'Xlsx',
        };

        $reader = IOFactory::createReader($readerType);
        // Solo datos (sin estilos, gráficas ni imágenes) — mejora de rendimiento significativa.
        $reader->setReadDataOnly(true);
        // Cargar únicamente las pestañas que necesitamos.
        if (method_exists($reader, 'setLoadSheetsOnly')) {
            $reader->setLoadSheetsOnly(['Container', 'PICS']);
        }

        $spreadsheet = $reader->load($filePath);

        $this->processContainerSheet($spreadsheet);
        $this->processPicsSheet($spreadsheet);
    }

    /**
     * Convierte un archivo XLSB a XLSX vía SheetJS (Node.js), sin depender de
     * Excel/COM. Retorna la ruta del archivo temporal XLSX, o null si la
     * conversión falla.
     */
    private function convertXlsbToXlsx(string $filePath): ?string
    {
        $realPath = realpath($filePath);
        if (!$realPath) {
            Log::warning('XLSB: no se pudo resolver realpath para el archivo subido.', ['filePath' => $filePath]);
            return null;
        }

        $tempPath   = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('xlsb_', true) . '.xlsx';
        $scriptPath = base_path('scripts/xlsb-to-xlsx/convert.js');

        $process = new Process([config('services.node.binary'), $scriptPath, $realPath, $tempPath]);
        $process->setTimeout(120);

        try {
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            Log::error('XLSB: falló la conversión vía SheetJS (Node.js).', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }

        return file_exists($tempPath) ? $tempPath : null;
    }

    // ─────────────────────────────────────────────────────────────────────
    // Pestaña "Container"
    // ─────────────────────────────────────────────────────────────────────

    private function processContainerSheet(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->getSheetByName('Container');
        if (!$sheet) {
            $this->errors[] = "No se encontró la pestaña 'Container' en el archivo.";
            return;
        }

        $assocRows = $this->sheetToAssocRows($sheet);
        $buffer    = []; // [date][container][item_code] => qty acumulada

        foreach ($assocRows as $row) {
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
            $customsDate = $this->parseDate(
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

        // Ventana de fechas a procesar: hasta 15 días a partir de hoy.
        $maxDate = Carbon::today()->addDays(15)->format('Y-m-d');
        $buffer  = array_filter($buffer, fn($date) => $date <= $maxDate, ARRAY_FILTER_USE_KEY);

        // Resolver items y costos en lote.
        // Forzamos string: SQL Server falla si intenta convertir la columna `code`
        // a int con códigos alfanuméricos.
        $allCodes = collect($buffer)
            ->flatMap(fn($byContainer) => collect($byContainer)->flatMap(fn($byItem) => array_keys($byItem)))
            ->map(fn($code) => (string) $code)
            ->unique()
            ->values();

        // SQL Server limita a 2100 parámetros por consulta — troceamos.
        $items = $allCodes
            ->chunk(2000)
            ->flatMap(fn($chunk) => Item::whereIn('code', $chunk->values())
                ->with(['lastCost.currency'])
                ->get())
            ->keyBy('code');

        ksort($buffer); // de más viejo a más reciente

        foreach ($buffer as $date => $byContainer) {
            ksort($byContainer);
            $containers = [];

            foreach ($byContainer as $containerCode => $byItem) {
                // Si el contenedor tiene al menos un item fuera del catálogo,
                // se descarta completo para no mezclar contenedores ajenos.
                $missingCodes = [];
                foreach ($byItem as $itemCode => $qty) {
                    if (!$items->has((string) $itemCode)) {
                        $missingCodes[] = (string) $itemCode;
                    }
                }

                if (!empty($missingCodes)) {
                    $this->containersSkipped++;
                    $this->itemsSkipped += count($missingCodes);
                    $this->errors[] = "Contenedor '{$containerCode}' descartado: item(s) ajeno(s) al catálogo (" . implode(', ', $missingCodes) . ").";
                    continue;
                }

                $containerTotal = 0.0;
                $linesOut       = [];

                foreach ($byItem as $itemCode => $qty) {
                    $itemCode = (string) $itemCode;
                    $item     = $items->get($itemCode);

                    $unitCost  = $item->lastCost ? (float) $item->lastCost->total_cost : 0.0;
                    $currency  = $item->lastCost?->currency?->code;
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

                $containers[] = [
                    'code'       => $containerCode,
                    'items'      => $linesOut,
                    'total_cost' => round($containerTotal, 4),
                ];
            }

            if (!empty($containers)) {
                $this->groups[] = [
                    'date'       => $date,
                    'date_label' => Carbon::parse($date)->format('d-m-Y'),
                    'containers' => $containers,
                ];
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    // Pestaña "PICS"
    // ─────────────────────────────────────────────────────────────────────

    private function processPicsSheet(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->getSheetByName('PICS');
        if (!$sheet) {
            return; // pestaña opcional — no genera error
        }

        $assocRows = $this->sheetToAssocRows($sheet);
        $rawTotals = []; // [item_code => qty acumulada]

        foreach ($assocRows as $row) {
            // La columna del número de parte puede llamarse PARTS NO o PART NO
            $partNo   = trim((string) ($row['parts_no'] ?? $row['part_no'] ?? ''));
            $totalQty = (float) ($row['total_qty'] ?? 0);

            if ($partNo === '' || $totalQty <= 0) {
                continue;
            }

            $rawTotals[$partNo] = ($rawTotals[$partNo] ?? 0) + $totalQty;
        }

        if (empty($rawTotals)) {
            return;
        }

        // Verificar existencia en catálogo — solo se conservan items registrados.
        $codes = collect(array_keys($rawTotals))->map(fn($c) => (string) $c);

        $existingCodes = $codes
            ->chunk(2000)
            ->flatMap(fn($chunk) => Item::whereIn('code', $chunk->values())->pluck('code'))
            ->flip() // [code => index] para lookup O(1)
            ->all();

        foreach ($rawTotals as $code => $qty) {
            $code = (string) $code;
            if (isset($existingCodes[$code])) {
                $this->picsItems[$code] = $qty;
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Convierte una hoja a un array de arrays asociativos,
     * usando la primera fila como encabezados normalizados (mismo formato
     * que Maatwebsite\Excel\WithHeadingRow).
     */
    private function sheetToAssocRows(Worksheet $sheet): array
    {
        // null = celdas vacías como null | false = no calcular fórmulas (los
        // datos del proveedor son valores planos — esto elimina el mayor cuello
        // de botella de rendimiento de PhpSpreadsheet)
        // false = valores sin formatear (fechas como número serial de Excel)
        // false = índices numéricos (0-based)
        $raw = $sheet->toArray(null, false, false, false);

        if (count($raw) < 2) {
            return [];
        }

        $headers = array_map(
            fn($h) => $this->normalizeHeader((string) ($h ?? '')),
            $raw[0]
        );

        $rows = [];
        for ($i = 1, $total = count($raw); $i < $total; $i++) {
            $assoc = [];
            foreach ($headers as $j => $header) {
                $assoc[$header] = $raw[$i][$j] ?? null;
            }
            $rows[] = $assoc;
        }

        return $rows;
    }

    /**
     * Normaliza un encabezado al mismo formato que Maatwebsite/Excel:
     * minúsculas, caracteres no alfanuméricos → guión bajo, sin guiones extremos.
     */
    private function normalizeHeader(string $h): string
    {
        $h = mb_strtolower(trim($h));
        $h = preg_replace('/[^a-z0-9]+/', '_', $h);
        return trim($h, '_');
    }

    private function parseDate(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return Carbon::createFromTimestamp(
                    ExcelDate::excelToTimestamp((float) $value)
                )->format('Y-m-d');
            } catch (\Exception) {
                return null;
            }
        }

        $str = trim((string) $value);

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
