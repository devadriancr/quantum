<?php

namespace App\Imports;

use App\Models\Container;
use App\Models\Item;
use App\Models\ShipmentDocument;
use App\Models\ShipmentDocumentLine;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ContainersImport implements ToCollection, WithHeadingRow
{
    public array $errors  = [];
    public int   $created = 0;
    public int   $skipped = 0;

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        // Agrupar filas por CT NO.
        $grouped = $rows->groupBy(fn($row) => trim($row['ct_no'] ?? ''));

        foreach ($grouped as $ctNo => $lines) {
            if (blank($ctNo)) {
                $this->skipped++;
                continue;
            }

            $firstLine = $lines->first();

            // --- Parsear fecha y hora ---
            $deliveryDate = $this->parseDate($firstLine['delivery_date'] ?? null);
            $deliveryTime = $this->parseTime($firstLine['delivery_time'] ?? null);

            $container = Container::where('code', $ctNo)
                ->where(function ($q) use ($deliveryDate) {
                    $deliveryDate
                        ? $q->where('estimated_arrival_date', $deliveryDate)
                        : $q->whereNull('estimated_arrival_date');
                })
                ->where(function ($q) use ($deliveryTime) {
                    $deliveryTime
                        ? $q->where('estimated_arrival_time', $deliveryTime)
                        : $q->whereNull('estimated_arrival_time');
                })
                ->first();

            if (! $container) {
                $container = Container::create([
                    'code'                   => $ctNo,
                    'partner_id'             => null,
                    'container_type'         => 'CONTAINER',
                    'estimated_arrival_date' => $deliveryDate,
                    'estimated_arrival_time' => $deliveryTime,
                    'status'                 => 'PENDING',
                ]);
            }

            // --- Buscar o crear el documento del contenedor ---
            $document = ShipmentDocument::firstOrCreate(
                ['container_id' => $container->id],
                [
                    'document_number'        => $ctNo,
                    'partner_id'             => null,
                    'document_date'          => $deliveryDate,
                    'document_time'          => $deliveryTime,
                    'estimated_arrival_date' => $deliveryDate,
                    'estimated_arrival_time' => $deliveryTime,
                    'document_status'        => 'PENDING',
                ]
            );

            // --- Crear las líneas ---
            $lineNumber = $document->shipmentDocumentLines()->max('line_number') ?? 0;

            // Pre-cargar seriales existentes en este documento para detectar duplicados eficientemente
            $existingSerials = $document->shipmentDocumentLines()
                ->whereNotNull('serial_number')
                ->pluck('serial_number')
                ->flip()
                ->all();

            foreach ($lines as $row) {
                $partNo   = trim($row['parts_no']  ?? '');
                $partsQty = $row['parts_qty']  ?? 0;
                $moduleNo = trim($row['module_no'] ?? '');

                if (blank($partNo)) {
                    $this->errors[] = "CT NO. {$ctNo}: fila sin PARTS NO., se omitió.";
                    $this->skipped++;
                    continue;
                }

                // Verificar serial duplicado dentro del mismo documento
                if ($moduleNo !== '' && isset($existingSerials[$moduleNo])) {
                    $this->errors[] = "CT NO. {$ctNo}: serial '{$moduleNo}' duplicado en este contenedor, línea omitida.";
                    $this->skipped++;
                    continue;
                }

                // Buscar el item por código
                $item = Item::where('code', $partNo)->first();

                if (! $item) {
                    $this->errors[] = "CT NO. {$ctNo}: item con código '{$partNo}' no encontrado, línea omitida.";
                    $this->skipped++;
                    continue;
                }

                $lineNumber++;

                ShipmentDocumentLine::create([
                    'shipment_document_id' => $document->id,
                    'line_number'          => $lineNumber,
                    'item_id'              => $item->id,
                    'serial_number'        => $moduleNo ?: null,
                    'quantity_declared'    => (float) $partsQty,
                    'status'               => 'PENDING',
                ]);

                // Registrar serial recién creado para evitar duplicados en la misma importación
                if ($moduleNo !== '') {
                    $existingSerials[$moduleNo] = true;
                }

                $this->created++;
            }
        }
    }

    private function parseDate(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        if (is_numeric($value)) {
            return Carbon::createFromTimestamp(
                \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value)
            )->format('Y-m-d');
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }

    private function parseTime(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        if (is_numeric($value) && $value < 1) {
            $seconds = (int) round($value * 86400);
            $h = intdiv($seconds, 3600);
            $m = intdiv($seconds % 3600, 60);
            $s = $seconds % 60;
            return sprintf('%02d:%02d:%02d', $h, $m, $s);
        }

        try {
            return Carbon::parse($value)->format('H:i:s');
        } catch (\Exception) {
            return null;
        }
    }
}
