<?php

namespace App\Http\Controllers;

use App\Models\InventoryBalance;
use App\Models\Item;
use App\Models\Location;
use App\Models\ShipmentDocumentLine;
use App\Models\StockMovement;
use App\Models\StockMovementLine;
use App\Models\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaterialOutputController extends Controller
{
    /**
     *
     */
    public function index(Request $request)
    {
        $search    = $request->get('search');
        $dateRange = $request->get('date_range');

        [$dateFrom, $dateTo] = $this->parseDateRange($dateRange);

        $movements = StockMovement::with(['lines', 'locationFrom', 'locationTo', 'createdBy'])
            ->where('movement_type', 'OUTBOUND')
            ->where(function ($q) {
                $q->whereHas('transactionType', fn($t) => $t->where('code', 'T'))
                    ->orWhereNull('transaction_type_id');
            })
            ->when($search, function ($q) use ($search) {
                $q->where('movement_number', 'like', "%{$search}%")
                    ->orWhereHas(
                        'lines.item',
                        fn($i) => $i
                            ->where('code', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%")
                    );
            })
            ->when($dateFrom && $dateTo, fn($q) => $q->whereBetween('movement_date', [$dateFrom, $dateTo]))
            ->orderByDesc('movement_date')
            ->orderByDesc('movement_time')
            ->paginate(10)
            ->withQueryString();

        return view('material-outputs.index', compact('movements', 'search', 'dateRange'));
    }

    /**
     *
     */
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
     *
     */
    public function store(Request $request)
    {
        $locationFrom    = Location::where('code', 'LIKE', 'L60%')->first();
        $locationTo      = Location::where('code', 'LIKE', 'L12%')->first();
        $transactionType = TransactionType::where('code', 'T')->where('status', 'ACTIVE')->first();

        $movement = StockMovement::create([
            'movement_number'     => 'OUT-' . now()->format('Ymd') . now()->format('His'),
            'movement_date'       => today()->toDateString(),
            'movement_time'       => now()->toTimeString(),
            'transaction_type_id' => $transactionType?->id,
            'movement_type'       => 'OUTBOUND',
            'location_id_from'    => $locationFrom?->id,
            'location_id_to'      => $locationTo?->id,
            'status'              => 'PENDING',
            'created_by_user_id'  => auth()->id(),
            'updated_by_user_id'  => auth()->id(),
        ]);

        return redirect()->route('material-outputs.show', $movement);
    }

    /**
     *
     */
    public function destroy(StockMovement $movement)
    {
        if ($movement->status === 'PENDING' && $movement->lines()->count() === 0) {
            $movement->delete();
        }

        return redirect()->route('material-outputs.index');
    }

    /**
     *
     */
    public function show(StockMovement $movement)
    {
        $movement->load(['locationFrom', 'locationTo', 'createdBy']);

        return view('material-outputs.show', compact('movement'));
    }

    /**
     *
     */
    public function scan(Request $request, StockMovement $movement)
    {
        if ($movement->status === 'COMPLETED') {
            return response()->json(['status' => 'error', 'message' => 'Este movimiento ya fue finalizado.'], 422);
        }

        $request->validate(['scan_content' => 'required|string']);

        DB::beginTransaction();
        try {
            $code            = trim($request->scan_content);
            $consignmentType = $this->detectConsignmentType($code);
            $parsed          = $this->parseCode($code, $consignmentType);

            if (! $parsed['part_no']) {
                DB::rollBack();
                return response()->json(['status' => 'error', 'message' => 'No se pudo extraer el número de parte.'], 422);
            }

            $item = Item::where('code', $parsed['part_no'])->first();
            if (! $item) {
                DB::rollBack();
                return response()->json(['status' => 'error', 'message' => 'Artículo no encontrado: ' . $parsed['part_no']], 422);
            }

            // Duplicado — excluye líneas ya devueltas (notes = 'RETURNED')
            if (! empty($parsed['serial'])) {
                $isDuplicate = StockMovementLine::where('serial_batch_number', $parsed['serial'])
                    ->where(fn($q) => $q->whereNull('notes')->orWhere('notes', '!=', 'RETURNED'))
                    ->whereHas('stockMovement', fn($q) => $q->where('movement_type', 'OUTBOUND'))
                    ->exists();

                if ($isDuplicate) {
                    DB::rollBack();
                    return response()->json([
                        'status'           => 'duplicate',
                        'message'          => 'Este serial ya tiene una salida activa registrada: ' . $parsed['serial'],
                        'item_code'        => $item->code,
                        'item_description' => $item->description,
                        'serial'           => $parsed['serial'],
                        'qty'              => $parsed['qty'],
                    ]);
                }
            }

            // ── Detección de ubicación y movimientos automáticos de trazabilidad ──
            $transactionType = TransactionType::where('code', 'T')->where('status', 'ACTIVE')->first();
            $warningMessage  = null;

            $balanceL60 = InventoryBalance::where('item_id', $item->id)
                ->where('location_id', $movement->location_id_from)
                ->first();

            if (! $balanceL60 || $balanceL60->current_quantity < $parsed['qty']) {
                $locationL61 = Location::where('code', 'LIKE', 'L61%')->first();
                $balanceL61  = $locationL61
                    ? InventoryBalance::where('item_id', $item->id)->where('location_id', $locationL61->id)->first()
                    : null;

                if ($balanceL61 && $balanceL61->current_quantity >= $parsed['qty']) {
                    // Material está en almacén externo (L61) sin retorno previo.
                    // Auto-generar retorno L61 → L60 para mantener trazabilidad.
                    $autoReturn = StockMovement::create([
                        'movement_number'     => 'RTN-AUTO-' . now()->format('YmdHis'),
                        'movement_date'       => today()->toDateString(),
                        'movement_time'       => now()->toTimeString(),
                        'transaction_type_id' => $transactionType?->id,
                        'movement_type'       => 'TRANSFER',
                        'location_id_from'    => $locationL61->id,
                        'location_id_to'      => $movement->location_id_from,
                        'notes'               => 'AUTO_RETURN_FROM_EXTERNAL',
                        'status'              => 'COMPLETED',
                        'created_by_user_id'  => auth()->id(),
                        'updated_by_user_id'  => auth()->id(),
                    ]);
                    StockMovementLine::create([
                        'stock_movement_id'   => $autoReturn->id,
                        'item_id'             => $item->id,
                        'quantity_received'   => $parsed['qty'],
                        'serial_batch_number' => $parsed['serial'] ?? null,
                        'unit_cost'           => $item->last_unit_cost,
                        'created_by_user_id'  => auth()->id(),
                        'updated_by_user_id'  => auth()->id(),
                    ]);
                    $balanceL61->current_quantity  -= $parsed['qty'];
                    $balanceL61->last_movement_id   = $autoReturn->id;
                    $balanceL61->last_movement_date = now();
                    $balanceL61->save();

                    $balanceL60 = InventoryBalance::firstOrCreate(
                        ['item_id' => $item->id, 'location_id' => $movement->location_id_from],
                        ['opening_quantity' => 0, 'current_quantity' => 0, 'reserved_quantity' => 0]
                    );
                    $balanceL60->current_quantity  += $parsed['qty'];
                    $balanceL60->last_movement_id   = $autoReturn->id;
                    $balanceL60->last_movement_date = now();
                    $balanceL60->save();

                    $warningMessage = "Advertencia: {$item->code} estaba en almacén externo sin recepción de retorno. Se generó {$autoReturn->movement_number} automáticamente para trazabilidad.";

                } else {
                    // No está en L61. Verificar si hay línea pendiente de contenedor.
                    $pendingLine = ShipmentDocumentLine::where('item_id', $item->id)
                        ->whereIn('status', ['PENDING', 'EXPECTED'])
                        ->when(! empty($parsed['serial']), fn($q) => $q->where('serial_number', $parsed['serial']))
                        ->first();

                    if ($pendingLine) {
                        // Auto-generar recepción de contenedor → L60.
                        $autoReceipt = StockMovement::create([
                            'movement_number'     => 'REC-AUTO-' . now()->format('YmdHis'),
                            'movement_date'       => today()->toDateString(),
                            'movement_time'       => now()->toTimeString(),
                            'transaction_type_id' => $transactionType?->id,
                            'movement_type'       => 'INBOUND',
                            'location_id_from'    => null,
                            'location_id_to'      => $movement->location_id_from,
                            'notes'               => "AUTO_RECEIPT_FROM_CONTAINER:{$pendingLine->shipment_document_id}",
                            'status'              => 'COMPLETED',
                            'created_by_user_id'  => auth()->id(),
                            'updated_by_user_id'  => auth()->id(),
                        ]);
                        StockMovementLine::create([
                            'stock_movement_id'         => $autoReceipt->id,
                            'shipment_document_line_id' => $pendingLine->id,
                            'item_id'                   => $item->id,
                            'quantity_received'         => $parsed['qty'],
                            'serial_batch_number'       => $parsed['serial'] ?? null,
                            'unit_cost'                 => $item->last_unit_cost,
                            'created_by_user_id'        => auth()->id(),
                            'updated_by_user_id'        => auth()->id(),
                        ]);
                        $balanceL60 = InventoryBalance::firstOrCreate(
                            ['item_id' => $item->id, 'location_id' => $movement->location_id_from],
                            ['opening_quantity' => 0, 'current_quantity' => 0, 'reserved_quantity' => 0]
                        );
                        $balanceL60->current_quantity  += $parsed['qty'];
                        $balanceL60->last_movement_id   = $autoReceipt->id;
                        $balanceL60->last_movement_date = now();
                        $balanceL60->save();

                        // Registrar recepción en la línea del documento de shipping
                        $totalReceived = StockMovementLine::where('shipment_document_line_id', $pendingLine->id)
                            ->sum('quantity_received');
                        $pendingLine->quantity_received = $totalReceived;
                        $pendingLine->status            = 'RECEIVED';
                        $pendingLine->save();

                        $warningMessage = "Advertencia: {$item->code} no tenía recepción de contenedor. Se generó {$autoReceipt->movement_number} automáticamente para trazabilidad.";
                    }
                    // Else: sin balance en ninguna ubicación conocida — se permite continuar
                    // (L60 puede quedar negativo; el equipo de almacén debe regularizar)
                }
            }

            // Obtener balance en L60 (ya actualizado si hubo auto-movimiento)
            $balance = InventoryBalance::firstOrCreate(
                ['item_id' => $item->id, 'location_id' => $movement->location_id_from],
                ['opening_quantity' => 0, 'current_quantity' => 0, 'reserved_quantity' => 0]
            );

            // Crear línea de salida (OUTBOUND — L60)
            $line = StockMovementLine::create([
                'stock_movement_id'   => $movement->id,
                'item_id'             => $item->id,
                'quantity_received'   => $parsed['qty'],
                'serial_batch_number' => $parsed['serial'] ?? null,
                'unit_cost'           => $item->last_unit_cost,
                'created_by_user_id'  => auth()->id(),
                'updated_by_user_id'  => auth()->id(),
            ]);

            // Crear línea de entrada (INBOUND — L12) en movimiento compañero
            $companion = $this->getOrCreateCompanionInbound($movement);
            StockMovementLine::create([
                'stock_movement_id'   => $companion->id,
                'item_id'             => $item->id,
                'quantity_received'   => $parsed['qty'],
                'serial_batch_number' => $parsed['serial'] ?? null,
                'unit_cost'           => $item->last_unit_cost,
                'notes'               => "companion_of_line:{$line->id}",
                'created_by_user_id'  => auth()->id(),
                'updated_by_user_id'  => auth()->id(),
            ]);

            // Disminuir L60, aumentar L12
            $balance->current_quantity  -= $parsed['qty'];
            $balance->last_movement_id   = $movement->id;
            $balance->last_movement_date = now();
            $balance->save();

            if ($movement->location_id_to) {
                $balanceTo = InventoryBalance::firstOrCreate(
                    ['item_id' => $item->id, 'location_id' => $movement->location_id_to],
                    ['opening_quantity' => 0, 'current_quantity' => 0, 'reserved_quantity' => 0]
                );
                $balanceTo->current_quantity  += $parsed['qty'];
                $balanceTo->last_movement_id   = $companion->id;
                $balanceTo->last_movement_date = now();
                $balanceTo->save();
            }

            DB::commit();

            return response()->json([
                'status'           => $warningMessage ? 'warning' : 'success',
                'message'          => $warningMessage ?? 'Salida registrada correctamente.',
                'item_code'        => $item->code,
                'item_description' => $item->description,
                'serial'           => $parsed['serial'] ?? null,
                'qty'              => $parsed['qty'],
                'consignment_type' => $consignmentType,
                'line_id'          => $line->id,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }

    /**
     *
     */
    public function complete(Request $request, StockMovement $movement)
    {
        if ($movement->status === 'COMPLETED') {
            return response()->json(['status' => 'error', 'message' => 'Ya fue finalizado.'], 422);
        }

        $movement->update([
            'status'             => 'COMPLETED',
            'updated_by_user_id' => auth()->id(),
        ]);

        // Completar también el movimiento INBOUND compañero
        StockMovement::where('notes', "companion_of:{$movement->movement_number}")
            ->update(['status' => 'COMPLETED', 'updated_by_user_id' => Auth::id()]);

        return response()->json(['status' => 'completed', 'message' => 'Entrega finalizada correctamente.']);
    }

    /**
     *
     */
    public function removeLine(StockMovement $movement, StockMovementLine $line)
    {
        if ($movement->status === 'COMPLETED') {
            return response()->json(['status' => 'error', 'message' => 'El movimiento ya fue finalizado.'], 422);
        }

        if ($line->stock_movement_id !== $movement->id) {
            return response()->json(['status' => 'error', 'message' => 'La línea no pertenece a este movimiento.'], 403);
        }

        DB::beginTransaction();
        try {
            $transactionType = TransactionType::where('code', 'T')->where('status', 'ACTIVE')->first();

            // Crear movimiento de retorno (L12 → L60)
            $returnMovement = StockMovement::create([
                'movement_number'     => 'RET-' . now()->format('YmdHis'),
                'movement_date'       => today()->toDateString(),
                'movement_time'       => now()->toTimeString(),
                'transaction_type_id' => $transactionType?->id,
                'movement_type'       => 'RETURN',
                'location_id_from'    => $movement->location_id_to,   // L12
                'location_id_to'      => $movement->location_id_from, // L60
                'notes'               => "Retorno de movimiento {$movement->movement_number}",
                'status'              => 'COMPLETED',
                'created_by_user_id'  => auth()->id(),
                'updated_by_user_id'  => auth()->id(),
            ]);

            // Línea del retorno
            StockMovementLine::create([
                'stock_movement_id'   => $returnMovement->id,
                'item_id'             => $line->item_id,
                'quantity_received'   => $line->quantity_received,
                'serial_batch_number' => $line->serial_batch_number,
                'unit_cost'           => $line->unit_cost,
                'created_by_user_id'  => auth()->id(),
                'updated_by_user_id'  => auth()->id(),
            ]);

            // Revertir inventario: L12 decrements, L60 increments
            if ($movement->location_id_to) {
                $balanceL12 = InventoryBalance::where('item_id', $line->item_id)
                    ->where('location_id', $movement->location_id_to)
                    ->first();
                if ($balanceL12) {
                    $balanceL12->current_quantity  -= $line->quantity_received;
                    $balanceL12->last_movement_id   = $returnMovement->id;
                    $balanceL12->last_movement_date = now();
                    $balanceL12->save();
                }
            }

            if ($movement->location_id_from) {
                $balanceL60 = InventoryBalance::firstOrCreate(
                    ['item_id' => $line->item_id, 'location_id' => $movement->location_id_from],
                    ['opening_quantity' => 0, 'current_quantity' => 0, 'reserved_quantity' => 0]
                );
                $balanceL60->current_quantity  += $line->quantity_received;
                $balanceL60->last_movement_id   = $returnMovement->id;
                $balanceL60->last_movement_date = now();
                $balanceL60->save();
            }

            // Marcar la línea OUTBOUND como devuelta (se conserva para el historial)
            $line->update(['notes' => 'RETURNED']);

            // Eliminar la línea INBOUND compañera que se creó al escanear
            StockMovementLine::where('notes', "companion_of_line:{$line->id}")->delete();

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Línea eliminada. Material retornado al almacén de recibo.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // ── Movimiento INBOUND compañero (L12) ──────────────────────────────────

    private function getOrCreateCompanionInbound(StockMovement $outbound): StockMovement
    {
        return StockMovement::firstOrCreate(
            ['notes' => "companion_of:{$outbound->movement_number}"],
            [
                'movement_number'     => 'INB-' . $outbound->movement_number,
                'movement_date'       => $outbound->movement_date,
                'movement_time'       => $outbound->movement_time,
                'transaction_type_id' => $outbound->transaction_type_id,
                'movement_type'       => 'INBOUND',
                'location_id_from'    => $outbound->location_id_from,
                'location_id_to'      => $outbound->location_id_to,
                'status'              => 'PENDING',
                'created_by_user_id'  => Auth::id(),
                'updated_by_user_id'  => Auth::id(),
            ]
        );
    }

    // ── Parsers (mismos que ReceptionScanController) ─────────────────────

    private function detectConsignmentType(string $code): string
    {
        if (str_starts_with($code, 'T1,')) return 'MY';
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
}
