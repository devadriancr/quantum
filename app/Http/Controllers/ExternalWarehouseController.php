<?php

namespace App\Http\Controllers;

use App\Models\InventoryBalance;
use App\Models\Item;
use App\Models\Location;
use App\Models\StockMovement;
use App\Models\StockMovementLine;
use App\Models\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExternalWarehouseController extends Controller
{
    // ── Listado de viajes a almacén externo ─────────────────────────────────

    public function index(Request $request)
    {
        $search    = $request->get('search');
        $dateRange = $request->get('date_range');
        $status    = $request->get('status');

        [$dateFrom, $dateTo] = $this->parseDateRange($dateRange);

        $locationL61 = Location::where('code', 'LIKE', 'L61%')->first();

        $movements = StockMovement::with(['lines', 'locationFrom', 'locationTo', 'createdBy'])
            ->where('movement_type', 'OUTBOUND')
            ->where('location_id_to', $locationL61?->id)
            ->whereHas('transactionType', fn($t) => $t->where('code', 'T'))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('movement_number', 'like', "%{$search}%")
                        ->orWhere('invoice_number', 'like', "%{$search}%")
                        ->orWhere('carta_porte', 'like', "%{$search}%")
                        ->orWhereHas('lines.item', fn($i) => $i->where('code', 'like', "%{$search}%"));
                });
            })
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($dateFrom && $dateTo, fn($q) => $q->whereBetween('movement_date', [$dateFrom, $dateTo]))
            ->orderByDesc('movement_date')
            ->orderByDesc('movement_time')
            ->paginate(10)
            ->withQueryString();

        return view('external-warehouse.index', compact('movements', 'search', 'dateRange', 'status'));
    }

    // ── Crear viaje (requiere Factura + Carta Porte) ─────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'invoice_number' => 'required|string|max:100',
            'carta_porte'    => 'required|string|max:100',
        ]);

        $locationFrom    = Location::where('code', 'LIKE', 'L60%')->first();
        $locationTo      = Location::where('code', 'LIKE', 'L61%')->first();
        $transactionType = TransactionType::where('code', 'T')->where('status', 'ACTIVE')->first();

        $movement = StockMovement::create([
            'movement_number'     => 'EXT-' . now()->format('Ymd') . now()->format('His'),
            'movement_date'       => today()->toDateString(),
            'movement_time'       => now()->toTimeString(),
            'transaction_type_id' => $transactionType?->id,
            'movement_type'       => 'OUTBOUND',
            'location_id_from'    => $locationFrom?->id,
            'location_id_to'      => $locationTo?->id,
            'invoice_number'      => $request->invoice_number,
            'carta_porte'         => $request->carta_porte,
            'status'              => 'PENDING',
            'created_by_user_id'  => auth()->id(),
            'updated_by_user_id'  => auth()->id(),
        ]);

        return redirect()->route('external-warehouse.show', $movement);
    }

    // ── Detalle / escaneo de salida ──────────────────────────────────────────

    public function show(StockMovement $movement)
    {
        $movement->load(['locationFrom', 'locationTo', 'createdBy']);

        return view('external-warehouse.show', compact('movement'));
    }

    // ── Escaneo de material (PENDING → L60 decrements, L61 increments) ───────

    public function scan(Request $request, StockMovement $movement)
    {
        if ($movement->status !== 'PENDING') {
            return response()->json(['status' => 'error', 'message' => 'Este viaje ya no permite escaneos.'], 422);
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

            if (! empty($parsed['serial'])) {
                $isDuplicate = StockMovementLine::where('serial_batch_number', $parsed['serial'])
                    ->where(fn($q) => $q->whereNull('notes')->orWhere('notes', '!=', 'RETURNED'))
                    ->whereHas('stockMovement', fn($q) => $q->where('movement_type', 'OUTBOUND'))
                    ->exists();

                if ($isDuplicate) {
                    DB::rollBack();
                    return response()->json([
                        'status'           => 'duplicate',
                        'message'          => 'Este serial ya tiene una salida activa: ' . $parsed['serial'],
                        'item_code'        => $item->code,
                        'item_description' => $item->description,
                        'serial'           => $parsed['serial'],
                        'qty'              => $parsed['qty'],
                    ]);
                }
            }

            $balanceFrom = InventoryBalance::firstOrCreate(
                ['item_id' => $item->id, 'location_id' => $movement->location_id_from],
                ['opening_quantity' => 0, 'current_quantity' => 0, 'reserved_quantity' => 0]
            );

            $line = StockMovementLine::create([
                'stock_movement_id'   => $movement->id,
                'item_id'             => $item->id,
                'quantity_received'   => $parsed['qty'],
                'serial_batch_number' => $parsed['serial'] ?? null,
                'unit_cost'           => $item->last_unit_cost,
                'created_by_user_id'  => auth()->id(),
                'updated_by_user_id'  => auth()->id(),
            ]);

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

            $balanceFrom->current_quantity  -= $parsed['qty'];
            $balanceFrom->last_movement_id   = $movement->id;
            $balanceFrom->last_movement_date = now();
            $balanceFrom->save();

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
                'status'           => 'success',
                'message'          => 'Material registrado en el viaje.',
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

    // ── Despachar viaje (PENDING → IN_TRANSIT / Embarcado) ──────────────────

    public function dispatch(StockMovement $movement)
    {
        if ($movement->status !== 'PENDING') {
            return response()->json(['status' => 'error', 'message' => 'Este viaje no puede ser enviado.'], 422);
        }

        $activeLines = $movement->lines()
            ->where(fn($q) => $q->whereNull('notes')->orWhere('notes', '!=', 'RETURNED'))
            ->count();

        if ($activeLines === 0) {
            return response()->json(['status' => 'error', 'message' => 'No hay material escaneado en este viaje.'], 422);
        }

        $movement->update([
            'status'             => 'IN_TRANSIT',
            'updated_by_user_id' => auth()->id(),
        ]);

        StockMovement::where('notes', "companion_of:{$movement->movement_number}")
            ->update(['status' => 'IN_TRANSIT', 'updated_by_user_id' => Auth::id()]);

        return response()->json(['status' => 'dispatched', 'message' => 'Viaje enviado. Material en tránsito hacia L61.']);
    }

    // ── Eliminar línea escaneada (solo mientras PENDING) ────────────────────

    public function removeLine(StockMovement $movement, StockMovementLine $line)
    {
        if ($movement->status !== 'PENDING') {
            return response()->json(['status' => 'error', 'message' => 'El viaje ya fue enviado.'], 422);
        }

        if ($line->stock_movement_id !== $movement->id) {
            return response()->json(['status' => 'error', 'message' => 'La línea no pertenece a este viaje.'], 403);
        }

        DB::beginTransaction();
        try {
            if ($movement->location_id_from) {
                $balanceFrom = InventoryBalance::firstOrCreate(
                    ['item_id' => $line->item_id, 'location_id' => $movement->location_id_from],
                    ['opening_quantity' => 0, 'current_quantity' => 0, 'reserved_quantity' => 0]
                );
                $balanceFrom->current_quantity  += $line->quantity_received;
                $balanceFrom->last_movement_date = now();
                $balanceFrom->save();
            }

            if ($movement->location_id_to) {
                $balanceTo = InventoryBalance::where('item_id', $line->item_id)
                    ->where('location_id', $movement->location_id_to)
                    ->first();
                if ($balanceTo) {
                    $balanceTo->current_quantity  -= $line->quantity_received;
                    $balanceTo->last_movement_date = now();
                    $balanceTo->save();
                }
            }

            StockMovementLine::where('notes', "companion_of_line:{$line->id}")->delete();

            $line->update(['notes' => 'RETURNED']);

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Material removido del viaje.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // ── Eliminar viaje (solo si vacío y PENDING) ─────────────────────────────

    public function destroy(StockMovement $movement)
    {
        if ($movement->status === 'PENDING' && $movement->lines()->whereNull('notes')->count() === 0) {
            StockMovement::where('notes', "companion_of:{$movement->movement_number}")->delete();
            $movement->delete();
        }

        return redirect()->route('external-warehouse.index');
    }

    // ── Listado de retornos de almacén externo ───────────────────────────────

    public function returnIndex(Request $request)
    {
        $search    = $request->get('search');
        $dateRange = $request->get('date_range');
        $status    = $request->get('status');

        [$dateFrom, $dateTo] = $this->parseDateRange($dateRange);

        $locationL61 = Location::where('code', 'LIKE', 'L61%')->first();
        $locationL60 = Location::where('code', 'LIKE', 'L60%')->first();

        $movements = StockMovement::with(['lines', 'locationFrom', 'locationTo', 'createdBy'])
            ->where('movement_type', 'TRANSFER')
            ->where('location_id_from', $locationL61?->id)
            ->where('location_id_to', $locationL60?->id)
            ->whereHas('transactionType', fn($t) => $t->where('code', 'T'))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('movement_number', 'like', "%{$search}%")
                        ->orWhere('invoice_number', 'like', "%{$search}%")
                        ->orWhere('carta_porte', 'like', "%{$search}%")
                        ->orWhereHas('lines.item', fn($i) => $i->where('code', 'like', "%{$search}%"));
                });
            })
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($dateFrom && $dateTo, fn($q) => $q->whereBetween('movement_date', [$dateFrom, $dateTo]))
            ->orderByDesc('movement_date')
            ->orderByDesc('movement_time')
            ->paginate(10)
            ->withQueryString();

        return view('external-warehouse-returns.index', compact('movements', 'search', 'dateRange', 'status'));
    }

    // ── Crear retorno (Factura + Carta Porte obligatorios) ───────────────────

    public function storeReturn(Request $request)
    {
        $request->validate([
            'invoice_number' => 'required|string|max:100',
            'carta_porte'    => 'required|string|max:100',
        ]);

        $locationFrom    = Location::where('code', 'LIKE', 'L61%')->first();
        $locationTo      = Location::where('code', 'LIKE', 'L60%')->first();
        $transactionType = TransactionType::where('code', 'T')->where('status', 'ACTIVE')->first();

        $movement = StockMovement::create([
            'movement_number'     => 'RTN-' . now()->format('YmdHis'),
            'movement_date'       => today()->toDateString(),
            'movement_time'       => now()->toTimeString(),
            'transaction_type_id' => $transactionType?->id,
            'movement_type'       => 'TRANSFER',
            'location_id_from'    => $locationFrom?->id,
            'location_id_to'      => $locationTo?->id,
            'invoice_number'      => $request->invoice_number,
            'carta_porte'         => $request->carta_porte,
            'status'              => 'PENDING',
            'created_by_user_id'  => auth()->id(),
            'updated_by_user_id'  => auth()->id(),
        ]);

        return redirect()->route('external-warehouse.return.show', $movement);
    }

    // ── Eliminar retorno (solo si vacío y PENDING) ───────────────────────────

    public function destroyReturn(StockMovement $movement)
    {
        if ($movement->status === 'PENDING' && $movement->lines()->count() === 0) {
            $movement->delete();
        }

        return redirect()->route('external-warehouse.return.index');
    }

    // ── Detalle del retorno (RTN-) ────────────────────────────────────────────

    public function showReturn(StockMovement $movement)
    {
        $movement->load(['locationFrom', 'locationTo', 'createdBy']);

        $originalNumber   = str_replace('retorno_de:', '', $movement->notes ?? '');
        $originalMovement = StockMovement::where('movement_number', $originalNumber)->first();

        return view('external-warehouse.return', compact('movement', 'originalMovement'));
    }

    // ── Escaneo de retorno (L61 decrements, L60 increments) ─────────────────

    public function scanReturn(Request $request, StockMovement $movement)
    {
        if ($movement->status !== 'PENDING') {
            return response()->json(['status' => 'error', 'message' => 'Este retorno ya fue finalizado.'], 422);
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

            // Validar que haya existencia real en L61 (de cualquier viaje)
            $balanceL61Check = InventoryBalance::where('item_id', $item->id)
                ->where('location_id', $movement->location_id_from) // L61
                ->first();

            if (! $balanceL61Check || $balanceL61Check->current_quantity < $parsed['qty']) {
                DB::rollBack();
                $disponible = $balanceL61Check?->current_quantity ?? 0;

                // Detectar si el material está en L60 para dar mensaje específico
                $balanceL60Check = InventoryBalance::where('item_id', $item->id)
                    ->where('location_id', $movement->location_id_to) // L60
                    ->first();

                $message = ($balanceL60Check && $balanceL60Check->current_quantity >= $parsed['qty'])
                    ? "Error: {$item->code} ya está en almacén (L60). No se requiere retorno — el material no está en almacén externo."
                    : "Sin existencia en almacén externo para {$item->code}. Disponible en externo: {$disponible}";

                return response()->json(['status' => 'error', 'message' => $message], 422);
            }

            if (! empty($parsed['serial'])) {
                $isDuplicate = StockMovementLine::where('serial_batch_number', $parsed['serial'])
                    ->where('stock_movement_id', $movement->id)
                    ->whereNull('notes')
                    ->exists();

                if ($isDuplicate) {
                    DB::rollBack();
                    return response()->json([
                        'status'  => 'duplicate',
                        'message' => 'Este serial ya fue registrado en este retorno.',
                        'serial'  => $parsed['serial'],
                    ]);
                }
            }

            $line = StockMovementLine::create([
                'stock_movement_id'   => $movement->id,
                'item_id'             => $item->id,
                'quantity_received'   => $parsed['qty'],
                'serial_batch_number' => $parsed['serial'] ?? null,
                'unit_cost'           => $item->last_unit_cost,
                'created_by_user_id'  => auth()->id(),
                'updated_by_user_id'  => auth()->id(),
            ]);

            // L61 → L60
            $balanceL61 = InventoryBalance::firstOrCreate(
                ['item_id' => $item->id, 'location_id' => $movement->location_id_from],
                ['opening_quantity' => 0, 'current_quantity' => 0, 'reserved_quantity' => 0]
            );
            $balanceL61->current_quantity  -= $parsed['qty'];
            $balanceL61->last_movement_id   = $movement->id;
            $balanceL61->last_movement_date = now();
            $balanceL61->save();

            $balanceL60 = InventoryBalance::firstOrCreate(
                ['item_id' => $item->id, 'location_id' => $movement->location_id_to],
                ['opening_quantity' => 0, 'current_quantity' => 0, 'reserved_quantity' => 0]
            );
            $balanceL60->current_quantity  += $parsed['qty'];
            $balanceL60->last_movement_id   = $movement->id;
            $balanceL60->last_movement_date = now();
            $balanceL60->save();

            DB::commit();

            return response()->json([
                'status'           => 'success',
                'message'          => 'Material registrado para retorno a L60.',
                'item_code'        => $item->code,
                'item_description' => $item->description,
                'serial'           => $parsed['serial'] ?? null,
                'qty'              => $parsed['qty'],
                'line_id'          => $line->id,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // ── Finalizar retorno (PENDING → COMPLETED) ──────────────────────────────

    public function completeReturn(StockMovement $movement)
    {
        if ($movement->status !== 'PENDING') {
            return response()->json(['status' => 'error', 'message' => 'Ya fue finalizado.'], 422);
        }

        if ($movement->lines()->count() === 0) {
            return response()->json(['status' => 'error', 'message' => 'No hay material escaneado para retornar.'], 422);
        }

        $movement->update([
            'status'             => 'COMPLETED',
            'updated_by_user_id' => auth()->id(),
        ]);

        return response()->json(['status' => 'completed', 'message' => 'Retorno finalizado. Material disponible en L60 para salida a producción.']);
    }

    // ── Helpers privados ─────────────────────────────────────────────────────

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
                'invoice_number'      => $outbound->invoice_number,
                'carta_porte'         => $outbound->carta_porte,
                'status'              => 'PENDING',
                'created_by_user_id'  => Auth::id(),
                'updated_by_user_id'  => Auth::id(),
            ]
        );
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
