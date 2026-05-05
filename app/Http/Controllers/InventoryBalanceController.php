<?php

namespace App\Http\Controllers;

use App\Exports\InventoryBalancesExport;
use App\Models\InventoryBalance;
use App\Models\StockMovementLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class InventoryBalanceController extends Controller
{
    public function index(Request $request)
    {
        $search    = $request->get('search');
        $warehouse = $request->get('warehouse');

        $balances = InventoryBalance::with(['item', 'location.warehouse'])
            ->whereHas('location', fn($q) => $q->where('code', 'like', 'L60%')->orWhere('code', 'like', 'L61%'))
            ->when($search, function ($q) use ($search) {
                $q->whereHas(
                    'item',
                    fn($i) => $i
                        ->where('code', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                )->orWhereHas(
                    'location',
                    fn($l) => $l
                        ->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                );
            })
            ->when($warehouse, function ($q) use ($warehouse) {
                $q->whereHas('location.warehouse', fn($w) => $w->where('id', $warehouse));
            })
            ->orderByDesc('updated_at')
            ->paginate(10)->withQueryString();

        $itemIds     = $balances->pluck('item_id')->unique()->values();
        $locationIds = $balances->pluck('location_id')->unique()->values();

        $lastInbounds   = collect();
        $outboundQtys   = collect();
        $returnQtys     = collect();
        $stockLimitsMap = collect();

        if ($itemIds->isNotEmpty()) {
            $lastInbounds = DB::table('stock_movements as sm')
                ->join('stock_movement_lines as sml', 'sm.id', '=', 'sml.stock_movement_id')
                ->select('sml.item_id', 'sm.location_id_to as location_id', 'sm.movement_date', 'sm.movement_time')
                ->where('sm.movement_type', 'INBOUND')
                ->whereIn('sml.item_id', $itemIds)
                ->whereIn('sm.location_id_to', $locationIds)
                ->orderByDesc('sm.movement_date')
                ->orderByDesc('sm.movement_time')
                ->get()
                ->groupBy(fn($r) => $r->item_id . '-' . $r->location_id)
                ->map(fn($g) => $g->first());

            // Salidas desde la ubicación hacia L12 (consumo a producción)
            $outboundQtys = DB::table('stock_movements as sm')
                ->join('stock_movement_lines as sml', 'sm.id', '=', 'sml.stock_movement_id')
                ->join('locations as loc_to', 'loc_to.id', '=', 'sm.location_id_to')
                ->select('sml.item_id', 'sm.location_id_from as location_id', DB::raw('SUM(sml.quantity_received) as total'))
                ->where('sm.movement_type', 'OUTBOUND')
                ->where('loc_to.code', 'like', 'L12%')
                ->whereIn('sml.item_id', $itemIds)
                ->whereIn('sm.location_id_from', $locationIds)
                ->groupBy('sml.item_id', 'sm.location_id_from')
                ->get()
                ->keyBy(fn($r) => $r->item_id . '-' . $r->location_id);

            // Devoluciones que regresaron a la ubicación (no cuentan como consumido)
            $returnQtys = DB::table('stock_movements as sm')
                ->join('stock_movement_lines as sml', 'sm.id', '=', 'sml.stock_movement_id')
                ->select('sml.item_id', 'sm.location_id_to as location_id', DB::raw('SUM(sml.quantity_received) as total'))
                ->where('sm.movement_type', 'RETURN')
                ->whereIn('sml.item_id', $itemIds)
                ->whereIn('sm.location_id_to', $locationIds)
                ->groupBy('sml.item_id', 'sm.location_id_to')
                ->get()
                ->keyBy(fn($r) => $r->item_id . '-' . $r->location_id);

            $stockLimitsMap = \App\Models\StockLimit::where('active', true)
                ->whereIn('item_id', $itemIds)
                ->whereIn('location_id', $locationIds)
                ->get()
                ->keyBy(fn($sl) => $sl->item_id . '-' . $sl->location_id);
        }

        return view('inventory-balances.index', compact(
            'balances',
            'search',
            'warehouse',
            'lastInbounds',
            'outboundQtys',
            'returnQtys',
            'stockLimitsMap'
        ));
    }

    public function export(Request $request)
    {
        return Excel::download(
            new InventoryBalancesExport($request->get('search'), $request->get('warehouse')),
            'Inventory_' . now()->format('YmdHis') . '.xlsx'
        );
    }

    public function show(Request $request, InventoryBalance $inventoryBalance)
    {
        $inventoryBalance->load(['item', 'location.warehouse']);

        $search    = $request->get('search');
        $dateRange = $request->get('date_range');

        [$dateFrom, $dateTo] = $this->parseDateRange($dateRange);

        $locationId = $inventoryBalance->location_id;

        $movements = StockMovementLine::query()
            ->with([
                'stockMovement.locationFrom.warehouse',
                'stockMovement.locationTo.warehouse',
                'stockMovement.container',
                'stockMovement.createdBy',
            ])
            ->join('stock_movements as sm', 'stock_movement_lines.stock_movement_id', '=', 'sm.id')
            ->where('stock_movement_lines.item_id', $inventoryBalance->item_id)
            ->where(function ($q) use ($locationId) {
                // Entradas/devoluciones/ajustes: el destino es esta ubicación
                $q->where(function ($inner) use ($locationId) {
                    $inner->whereIn('sm.movement_type', ['INBOUND', 'RETURN', 'ADJUSTMENT'])
                        ->where('sm.location_id_to', $locationId);
                })
                // Salidas/traspasos: el origen es esta ubicación
                ->orWhere(function ($inner) use ($locationId) {
                    $inner->whereIn('sm.movement_type', ['OUTBOUND', 'TRANSFER'])
                        ->where('sm.location_id_from', $locationId);
                });
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('sm.movement_number', 'like', "%{$search}%")
                        ->orWhere('stock_movement_lines.serial_batch_number', 'like', "%{$search}%")
                        ->orWhereHas('stockMovement.container', fn($c) => $c->where('code', 'like', "%{$search}%"));
                });
            })
            ->when($dateFrom, fn($q) => $q->where('sm.movement_date', '>=', $dateFrom))
            ->when($dateTo,   fn($q) => $q->where('sm.movement_date', '<=', $dateTo))
            ->orderByDesc('sm.movement_date')
            ->orderByDesc('sm.movement_time')
            ->select('stock_movement_lines.*')
            ->paginate(15)
            ->withQueryString();

        return view('inventory-balances.show', compact('inventoryBalance', 'movements', 'search', 'dateRange'));
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
}
