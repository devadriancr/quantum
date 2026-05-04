<?php

namespace App\Http\Controllers;

use App\Models\InventoryBalance;
use App\Models\Item;
use App\Models\Location;
use App\Models\ReceptionScan;
use App\Models\ShipmentDocument;
use App\Models\ShipmentDocumentLine;
use App\Models\StockMovement;
use App\Models\StockMovementLine;
use App\Models\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceptionScanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search    = $request->get('search');
        $dateRange = $request->get('date_range');

        [$dateFrom, $dateTo] = $this->parseDateRange($dateRange);

        $documents = ShipmentDocument::with('partner', 'container')
            // ->whereIn('document_status', ['PENDING', 'PARTIAL'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('document_number', 'like', "%{$search}%")
                        ->orWhereHas('container', fn($c) => $c->where('code', 'like', "%{$search}%"))
                        ->orWhereHas('partner', fn($p) => $p->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($dateFrom && $dateTo, fn($q) =>
                $q->whereBetween('document_date', [$dateFrom, $dateTo])
            )
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('reception-scans.index', compact('documents', 'search', 'dateRange'));
    }

    private function parseDateRange(?string $dateRange): array
    {
        if (blank($dateRange) || ! str_contains($dateRange, ' - ')) {
            return [null, null];
        }

        [$fromStr, $toStr] = explode(' - ', $dateRange, 2);

        try {
            return [
                \Carbon\Carbon::parse(trim($fromStr))->format('Y-m-d'),
                \Carbon\Carbon::parse(trim($toStr))->format('Y-m-d'),
            ];
        } catch (\Exception) {
            return [null, null];
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ShipmentDocument $shipmentDocument)
    {
        $locationTo = Location::where('code', 'LIKE', 'L60%')->first();

        $movement = StockMovement::where('shipment_document_id', $shipmentDocument->id)
            ->whereIn('status', ['PENDING', 'RECEIVED'])
            ->latest()
            ->first();

        if (! $movement) {
            // Busca cualquier tipo de transacción RECEIPT activo
            $transactionType = TransactionType::where('transaction_category', 'RECEIPT')
                ->where('status', 'ACTIVE')
                ->first();

            $movement = StockMovement::create([
                'movement_number'      => 'REC-' . now()->format('YmdHis'),
                'movement_date'        => now()->toDateString(),
                'movement_time'        => now()->toTimeString(),
                'transaction_type_id'  => $transactionType?->id,
                'movement_type'        => 'INBOUND',
                'location_id_from'     => null,
                'location_id_to'       => $locationTo?->id,
                'container_id'         => $shipmentDocument->container_id,
                'shipment_document_id' => $shipmentDocument->id,
                'partner_id'           => $shipmentDocument->partner_id,
                'status'               => 'PENDING',
            ]);
        }
        dd($movement);
        $scans = ReceptionScan::where('stock_movement_id', $movement->id)
            ->with('matchedDocumentLine.item')
            ->orderByDesc('id')
            ->get();

        // Cargamos las líneas con la cantidad ya recibida calculada desde movement lines
        $documentLines = $shipmentDocument->shipmentDocumentLines()
            ->with(['item', 'stockMovementLines'])
            ->get()
            ->map(function ($line) {
                // Cantidad recibida calculada desde las líneas de movimiento reales
                $line->quantity_received_computed = $line->stockMovementLines->sum('quantity_received');
                return $line;
            });

        $stats = [
            'matched'   => $scans->where('scan_status', 'MATCHED')->count(),
            'unmatched' => $scans->where('scan_status', 'UNMATCHED')->count(),
            'duplicate' => $scans->where('scan_status', 'DUPLICATE')->count(),
            'error'     => $scans->where('scan_status', 'ERROR')->count(),
            'total'     => $scans->count(),
        ];

        return view('reception-scans.show', compact(
            'shipmentDocument',
            'movement',
            'locationTo',
            'scans',
            'documentLines',
            'stats'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function scan(Request $request, ShipmentDocument $shipmentDocument)
    {
        $request->validate([
            'scan_content'      => 'required|string',
            'stock_movement_id' => 'required|exists:stock_movements,id',
        ]);

        DB::beginTransaction();
        try {
            $code     = trim($request->scan_content);
            $movement = StockMovement::findOrFail($request->stock_movement_id);

            // 1 — Detectar tipo de consigna
            $consignmentType = $this->detectConsignmentType($code);

            // 2 — Parsear código
            $parsed = $this->parseCode($code, $consignmentType);

            if (! $parsed['part_no']) {
                return $this->scanError($movement, $shipmentDocument, $code, $consignmentType, 'No se pudo extraer el número de parte.');
            }

            // 3 — Buscar artículo
            $item = Item::where('code', $parsed['part_no'])->first();

            if (! $item) {
                return $this->scanUnmatched($movement, $shipmentDocument, $code, $consignmentType, $parsed, 'Artículo no encontrado: ' . $parsed['part_no']);
            }

            // 4 — Verificar duplicado (MY y MC tienen serial único)
            if (in_array($consignmentType, ['MY', 'MC']) && ! empty($parsed['serial'])) {
                $isDuplicate = ReceptionScan::where('stock_movement_id', $movement->id)
                    ->where('parsed_serial', $parsed['serial'])
                    ->where('scan_status', 'MATCHED')
                    ->exists();

                if ($isDuplicate) {
                    ReceptionScan::create([
                        'stock_movement_id'    => $movement->id,
                        'shipment_document_id' => $shipmentDocument->id,
                        'scan_content'         => $code,
                        'consignment_type'     => $consignmentType,
                        'parsed_serial'        => $parsed['serial'],
                        'parsed_item_code'     => $parsed['part_no'],
                        'parsed_supplier'      => $parsed['supplier'] ?? null,
                        'parsed_quantity'      => $parsed['qty'],
                        'scan_status'          => 'DUPLICATE',
                        'error_message'        => 'Serial ya escaneado en este movimiento.',
                    ]);

                    DB::commit();
                    return response()->json([
                        'status'           => 'duplicate',
                        'message'          => 'Serial ya escaneado: ' . $parsed['serial'],
                        'item_code'        => $item->code,
                        'item_description' => $item->description,
                        'serial'           => $parsed['serial'],
                        'qty'              => $parsed['qty'],
                        'consignment_type' => $consignmentType,
                    ]);
                }
            }

            // 5 — Buscar línea de documento que coincida
            $docLine = $this->findDocumentLine($shipmentDocument, $item, $parsed, $consignmentType);

            // 6 — Crear línea de movimiento
            $movementLine = StockMovementLine::create([
                'stock_movement_id'         => $movement->id,
                'item_id'                   => $item->id,
                'shipment_document_line_id' => $docLine?->id,
                'quantity_received'         => $parsed['qty'],
                'unit_cost'                 => $item->last_unit_cost,
                'serial_batch_number'       => $parsed['serial'] ?? null,
            ]);

            // 7 — Actualizar InventoryBalance
            $this->updateInventoryBalance($item, $movement->location_id_to, $parsed['qty'], $movement->id);

            // 8 — Calcular total recibido para la línea del documento y actualizar estado
            $totalReceived   = 0;
            $quantityDeclared = 0;

            if ($docLine) {
                // Suma TODAS las movement lines vinculadas a esta doc line (incluye la que acabamos de crear)
                $totalReceived    = StockMovementLine::where('shipment_document_line_id', $docLine->id)
                    ->sum('quantity_received');
                $quantityDeclared = (float) $docLine->quantity_declared;

                if ($totalReceived >= $quantityDeclared) {
                    $docLine->status = 'RECEIVED';
                    $docLine->save();
                }
            }

            // 9 — Registrar el scan
            $scan = ReceptionScan::create([
                'stock_movement_id'        => $movement->id,
                'shipment_document_id'     => $shipmentDocument->id,
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

            DB::commit();

            return response()->json([
                'status'            => $docLine ? 'matched' : 'unmatched',
                'message'           => $docLine ? 'Escaneado correctamente' : 'Artículo sin línea de documento asociada',
                'item_code'         => $item->code,
                'item_description'  => $item->description,
                'serial'            => $parsed['serial'] ?? null,
                'qty'               => $parsed['qty'],
                'consignment_type'  => $consignmentType,
                'scan_id'           => $scan->id,
                'doc_line_id'       => $docLine?->id,
                'total_received'    => $totalReceived,
                'quantity_declared' => $quantityDeclared,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Error interno: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function complete(Request $request, ShipmentDocument $shipmentDocument)
    {
        $request->validate(['stock_movement_id' => 'required|exists:stock_movements,id']);

        $movement = StockMovement::findOrFail($request->stock_movement_id);

        DB::transaction(function () use ($movement, $shipmentDocument) {
            $movement->update(['status' => 'COMPLETED']);

            $shipmentDocument->update(['document_status' => 'COMPLETE']);

            if ($shipmentDocument->container_id) {
                $shipmentDocument->container->update(['status' => 'RECEIVED']);
            }
        });

        return response()->json(['status' => 'completed', 'message' => 'Recepción completada exitosamente.']);
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

        // MC / MH
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

    private function findDocumentLine(
        ShipmentDocument $document,
        Item $item,
        array $parsed,
        string $type
    ): ?ShipmentDocumentLine {
        $query = ShipmentDocumentLine::where('shipment_document_id', $document->id)
            ->where('item_id', $item->id);

        if ($type === 'MY' && ! empty($parsed['serial'])) {
            return $query->where('serial_number', $parsed['serial'])->first();
        }

        // MC / MH — primera línea que aún no esté completamente recibida
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

    private function scanUnmatched(
        StockMovement $movement,
        ShipmentDocument $document,
        string $code,
        string $type,
        array $parsed,
        string $reason
    ) {
        ReceptionScan::create([
            'stock_movement_id'    => $movement->id,
            'shipment_document_id' => $document->id,
            'scan_content'         => $code,
            'consignment_type'     => $type,
            'parsed_serial'        => $parsed['serial'] ?? null,
            'parsed_item_code'     => $parsed['part_no'] ?? null,
            'parsed_quantity'      => $parsed['qty'] ?? null,
            'scan_status'          => 'UNMATCHED',
            'error_message'        => $reason,
        ]);

        DB::commit();
        return response()->json([
            'status'  => 'unmatched',
            'message' => $reason,
            'item_code' => $parsed['part_no'] ?? null,
            'qty'       => $parsed['qty'] ?? 0,
            'consignment_type' => $type,
        ]);
    }

    private function scanError(
        StockMovement $movement,
        ShipmentDocument $document,
        string $code,
        string $type,
        string $reason
    ) {
        ReceptionScan::create([
            'stock_movement_id'    => $movement->id,
            'shipment_document_id' => $document->id,
            'scan_content'         => $code,
            'consignment_type'     => $type,
            'scan_status'          => 'ERROR',
            'error_message'        => $reason,
        ]);

        DB::commit();
        return response()->json(['status' => 'error', 'message' => $reason], 422);
    }
}
