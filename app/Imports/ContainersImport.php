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
    public array $errors   = [];
    public int   $created  = 0;
    public int   $skipped  = 0;

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

            // --- Crear o recuperar el contenedor ---
            $container = Container::firstOrCreate(
                ['code' => $ctNo],
                [
                    'partner_id'             => null,
                    'container_type'         => 'CONTAINER',
                    'estimated_arrival_date' => $deliveryDate,
                    'estimated_arrival_time' => $deliveryTime,
                    'status'                 => 'PENDING',
                ]
            );

            // --- Crear el documento si no existe ---
            $docNumber = 'DOC-' . $ctNo;

            $document = ShipmentDocument::firstOrCreate(
                ['document_number' => $docNumber, 'container_id' => $container->id],
                [
                    'container_id'           => $container->id,
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

            foreach ($lines as $row) {
                $partNo  = trim($row['parts_no']  ?? '');
                $partsQty = $row['parts_qty']  ?? 0;
                $moduleNo = trim($row['module_no'] ?? '');

                if (blank($partNo)) {
                    $this->errors[] = "CT NO. {$ctNo}: fila sin PARTS NO., se omitió.";
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

                $this->created++;
            }
        }
    }

    private function parseDate($value): ?string
    {
        if (blank($value)) {
            return null;
        }

        // Excel puede pasar fechas como número serial o como string
        if (is_numeric($value)) {
            return Carbon::createFromTimestamp(
                \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value)
            )->format('Y-m-d');
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseTime($value): ?string
    {
        if (blank($value)) {
            return null;
        }

        // Excel puede pasar tiempos como fracción decimal (ej. 0.5 = 12:00)
        if (is_numeric($value) && $value < 1) {
            $seconds = (int) round($value * 86400);
            $h = intdiv($seconds, 3600);
            $m = intdiv($seconds % 3600, 60);
            $s = $seconds % 60;
            return sprintf('%02d:%02d:%02d', $h, $m, $s);
        }

        try {
            return Carbon::parse($value)->format('H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }
}
