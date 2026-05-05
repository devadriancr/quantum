<?php

namespace App\Http\Controllers;

use App\Models\InventoryBalance;
use App\Models\StockMovementLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryBalanceController extends Controller
{
    public function index(Request $request)
    {
        $search    = $request->get('search');
        $warehouse = $request->get('warehouse');

        $balances = InventoryBalance::with(['item', 'location.warehouse'])
            ->whereHas('location', fn($q) => $q->where('code', 'like', 'L60%'))
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

            // Salidas desde la ubicación
            $outboundQtys = DB::table('stock_movements as sm')
                ->join('stock_movement_lines as sml', 'sm.id', '=', 'sml.stock_movement_id')
                ->select('sml.item_id', 'sm.location_id_from as location_id', DB::raw('SUM(sml.quantity_received) as total'))
                ->where('sm.movement_type', 'OUTBOUND')
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

    public function show(InventoryBalance $inventoryBalance)
    {
        $inventoryBalance->load(['item', 'location.warehouse']);

        $movements = StockMovementLine::with([
            'item',
            'stockMovement.locationFrom',
            'stockMovement.locationTo',
            'stockMovement.container',
            'stockMovement.createdBy',
        ])
            ->where('item_id', $inventoryBalance->item_id)
            ->whereHas('stockMovement', function ($q) use ($inventoryBalance) {
                $q->where(function ($inner) use ($inventoryBalance) {
                    $inner->where('location_id_from', $inventoryBalance->location_id)
                          ->orWhere('location_id_to', $inventoryBalance->location_id);
                })
                ->where(function ($inner) {
                    $inner->whereHas('locationFrom', fn($l) => $l->where('code', 'like', 'L60%'))
                          ->orWhereHas('locationTo',   fn($l) => $l->where('code', 'like', 'L60%'));
                });
            })
            ->join('stock_movements', 'stock_movements.id', '=', 'stock_movement_lines.stock_movement_id')
            ->orderByDesc('stock_movements.updated_at')
            ->select('stock_movement_lines.*')
            ->paginate(10)
            ->withQueryString();

        return view('inventory-balances.show', compact('inventoryBalance', 'movements'));
    }
}
