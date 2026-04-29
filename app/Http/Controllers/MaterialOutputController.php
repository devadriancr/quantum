<?php

namespace App\Http\Controllers;

use App\Models\InventoryBalance;
use App\Models\Item;
use App\Models\Location;
use App\Models\StockMovement;
use App\Models\StockMovementLine;
use App\Models\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialOutputController extends Controller
{
    // ── Lista de entregas ────────────────────────────────────────────────

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
                  ->orWhereHas('lines.item', fn($i) => $i
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

    // ── Crear nuevo movimiento y redirigir al escaneo ────────────────────

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

    // ── Regresar: eliminar movimiento si está vacío ──────────────────────

    public function destroy(StockMovement $movement)
    {
        if ($movement->status === 'PENDING' && $movement->lines()->count() === 0) {
            $movement->delete();
        }

        return redirect()->route('material-outputs.index');
    }

    // ── Página de escaneo del movimiento ─────────────────────────────────

    public function show(StockMovement $movement)
    {
        $movement->load(['locationFrom', 'locationTo', 'createdBy']);

        return view('material-outputs.show', compact('movement'));
    }

    // ── Procesar escaneo (AJAX) ──────────────────────────────────────────

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

            // Duplicado — el mismo serial no puede estar activo en ninguna salida
            // (Si fue devuelto, la línea OUTBOUND se elimina, por lo que quedaría libre)
            if (! empty($parsed['serial'])) {
                $isDuplicate = StockMovementLine::where('serial_batch_number', $parsed['serial'])
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

            // Obtener o crear balance en L60 (permite negativos)
            $balance = InventoryBalance::firstOrCreate(
                ['item_id' => $item->id, 'location_id' => $movement->location_id_from],
                ['opening_quantity' => 0, 'current_quantity' => 0, 'reserved_quantity' => 0]
            );

            // Crear línea de movimiento
            $line = StockMovementLine::create([
                'stock_movement_id'   => $movement->id,
                'item_id'             => $item->id,
                'quantity_received'   => $parsed['qty'],
                'serial_batch_number' => $parsed['serial'] ?? null,
                'unit_cost'           => $item->last_unit_cost,
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
                $balanceTo->last_movement_id   = $movement->id;
                $balanceTo->last_movement_date = now();
                $balanceTo->save();
            }

            DB::commit();

            return response()->json([
                'status'           => 'success',
                'message'          => 'Salida registrada correctamente.',
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

    // ── Finalizar movimiento ─────────────────────────────────────────────

    public function complete(Request $request, StockMovement $movement)
    {
        if ($movement->status === 'COMPLETED') {
            return response()->json(['status' => 'error', 'message' => 'Ya fue finalizado.'], 422);
        }

        $movement->update([
            'status'             => 'COMPLETED',
            'updated_by_user_id' => auth()->id(),
        ]);

        return response()->json(['status' => 'completed', 'message' => 'Entrega finalizada correctamente.']);
    }

    // ── Eliminar línea y crear retorno al almacén ────────────────────────

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

            // Eliminar la línea del movimiento de salida
            $line->delete();

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
