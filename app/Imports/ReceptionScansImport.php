<?php

namespace App\Imports;

use App\Models\InventoryBalance;
use App\Models\Item;
use App\Models\ReceptionScan;
use App\Models\ShipmentDocument;
use App\Models\ShipmentDocumentLine;
use App\Models\StockMovement;
use App\Models\StockMovementLine;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ReceptionScansImport implements ToCollection, WithHeadingRow
{
    public int   $processed = 0;
    public int   $skipped   = 0;
    public array $errors    = [];

    private ShipmentDocument $document;
    private StockMovement    $movement;

    public function __construct(ShipmentDocument $document, StockMovement $movement)
    {
        $this->document = $document;
        $this->movement = $movement;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $code = trim($row['code'] ?? '');

            if (blank($code)) {
                $this->skipped++;
                continue;
            }

            DB::beginTransaction();
            try {
                $this->processCode($code);
                DB::commit();
                $this->processed++;
            } catch (\Throwable $e) {
                DB::rollBack();
                $this->errors[] = "Código '{$code}': " . $e->getMessage();
                $this->skipped++;
            }
        }
    }

    private function processCode(string $code): void
    {
        $consignmentType = $this->detectConsignmentType($code);
        $parsed          = $this->parseCode($code, $consignmentType);

        if (! $parsed['part_no']) {
            ReceptionScan::create([
                'stock_movement_id'    => $this->movement->id,
                'shipment_document_id' => $this->document->id,
                'scan_content'         => $code,
                'consignment_type'     => $consignmentType,
                'scan_status'          => 'ERROR',
                'error_message'        => 'No se pudo extraer el número de parte.',
            ]);
            return;
        }

        $item = Item::where('code', $parsed['part_no'])->first();

        if (! $item) {
            ReceptionScan::create([
                'stock_movement_id'    => $this->movement->id,
                'shipment_document_id' => $this->document->id,
                'scan_content'         => $code,
                'consignment_type'     => $consignmentType,
                'parsed_item_code'     => $parsed['part_no'],
                'parsed_quantity'      => $parsed['qty'],
                'scan_status'          => 'UNMATCHED',
                'error_message'        => 'Artículo no encontrado: ' . $parsed['part_no'],
            ]);
            return;
        }

        if (in_array($consignmentType, ['MY', 'MC']) && ! empty($parsed['serial'])) {
            $isDuplicate = ReceptionScan::where('stock_movement_id', $this->movement->id)
                ->where('parsed_serial', $parsed['serial'])
                ->where('scan_status', 'MATCHED')
                ->exists();

            if ($isDuplicate) {
                ReceptionScan::create([
                    'stock_movement_id'    => $this->movement->id,
                    'shipment_document_id' => $this->document->id,
                    'scan_content'         => $code,
                    'consignment_type'     => $consignmentType,
                    'parsed_serial'        => $parsed['serial'],
                    'parsed_item_code'     => $parsed['part_no'],
                    'parsed_supplier'      => $parsed['supplier'] ?? null,
                    'parsed_quantity'      => $parsed['qty'],
                    'scan_status'          => 'DUPLICATE',
                    'error_message'        => 'Serial ya escaneado en este movimiento.',
                ]);
                return;
            }
        }

        $docLine = $this->findDocumentLine($item, $parsed, $consignmentType);

        $movementLine = StockMovementLine::create([
            'stock_movement_id'         => $this->movement->id,
            'item_id'                   => $item->id,
            'shipment_document_line_id' => $docLine?->id,
            'quantity_received'         => $parsed['qty'],
            'unit_cost'                 => $item->last_unit_cost,
            'serial_batch_number'       => $parsed['serial'] ?? null,
        ]);

        $this->updateInventoryBalance($item, $this->movement->location_id_to, $parsed['qty'], $this->movement->id);

        if ($docLine) {
            $totalReceived = StockMovementLine::where('shipment_document_line_id', $docLine->id)
                ->sum('quantity_received');

            if ($totalReceived >= (float) $docLine->quantity_declared) {
                $docLine->status = 'RECEIVED';
                $docLine->save();
            }
        }

        ReceptionScan::create([
            'stock_movement_id'        => $this->movement->id,
            'shipment_document_id'     => $this->document->id,
            'scan_content'             => $code,
            'consignment_type'         => $consignmentType,
            'parsed_serial'            => $parsed['serial'] ?? null,
            'parsed_item_code'         => $parsed['part_no'],
            'parsed_supplier'          => $parsed['supplier'] ?? null,
            'parsed_quantity'          => $parsed['qty'],
            'scan_status'              => $docLine ? 'MATCHED' : 'UNMATCHED',
            'matched_document_line_id' => $docLine?->id,
            'stock_movement_line_id'   => $movementLine->id,
        ]);
    }

    private function detectConsignmentType(string $code): string
    {
        if (str_starts_with($code, 'T1,')) {
            return 'MY';
        }
        if (strlen($code) >= 33) {
            $type = substr($code, 31, 2);
            if ($type === 'MC') return 'MC';
            if ($type === 'MH') return 'MH';
        }
        return 'UNKNOWN';
    }

    private function parseCode(string $code, string $type): array
    {
        if ($type === 'MY') {
            $parts    = array_map('trim', explode(',', $code));
            $supplier = $parts[11] ?? '';
            $serial   = $parts[13] ?? '';
            return [
                'supplier' => $supplier,
                'serial'   => $supplier . $serial,
                'part_no'  => trim(end($parts)),
                'qty'      => (float) ($parts[10] ?? 1),
            ];
        }

        $snpRaw = substr($code, 20, 6);
        $qty    = (float) ltrim($snpRaw, '0') ?: 1;
        return [
            'serial'   => substr($code, 0, 10),
            'part_no'  => trim(substr($code, 10, 10)),
            'snp'      => $snpRaw,
            'supplier' => trim(substr($code, 26, 5)),
            'qty'      => $qty,
        ];
    }

    private function findDocumentLine(Item $item, array $parsed, string $type): ?ShipmentDocumentLine
    {
        $query = ShipmentDocumentLine::where('shipment_document_id', $this->document->id)
            ->where('item_id', $item->id);

        if ($type === 'MY' && ! empty($parsed['serial'])) {
            return $query->where('serial_number', $parsed['serial'])->first();
        }

        return $query->where('status', '!=', 'RECEIVED')->first();
    }

    private function updateInventoryBalance(Item $item, ?int $locationId, float $qty, int $movementId): void
    {
        if (! $locationId) return;

        $balance = InventoryBalance::firstOrCreate(
            ['item_id' => $item->id, 'location_id' => $locationId],
            ['opening_quantity' => 0, 'current_quantity' => 0, 'reserved_quantity' => 0]
        );

        $balance->current_quantity  += $qty;
        $balance->last_movement_id   = $movementId;
        $balance->last_movement_date = now();
        $balance->save();
    }
}
