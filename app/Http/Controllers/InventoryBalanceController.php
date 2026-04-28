<?php

namespace App\Http\Controllers;

use App\Models\InventoryBalance;
use App\Models\StockMovementLine;
use Illuminate\Http\Request;

class InventoryBalanceController extends Controller
{
    public function index(Request $request)
    {
        $search    = $request->get('search');
        $warehouse = $request->get('warehouse');

        $balances = InventoryBalance::with(['item', 'location.warehouse'])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('item', fn($i) => $i
                    ->where('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                )->orWhereHas('location', fn($l) => $l
                    ->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                );
            })
            ->when($warehouse, function ($q) use ($warehouse) {
                $q->whereHas('location.warehouse', fn($w) => $w->where('id', $warehouse));
            })
            ->orderBy('inventory_balances', 'desc')
            ->paginate(10);

        $warehouses = \App\Models\Warehouse::where('status', 'OPERATIONAL')->orderBy('name')->get();

        return view('inventory-balances.index', compact('balances', 'search', 'warehouses', 'warehouse'));
    }

    public function show(InventoryBalance $inventoryBalance)
    {
        $inventoryBalance->load(['item', 'location.warehouse']);

        $movements = StockMovementLine::with([
            'stockMovement.locationFrom',
            'stockMovement.locationTo',
            'stockMovement.transactionType',
            'stockMovement.createdBy',
        ])
        ->where('item_id', $inventoryBalance->item_id)
        ->whereHas('stockMovement', function ($q) use ($inventoryBalance) {
            $q->where('location_id_from', $inventoryBalance->location_id)
              ->orWhere('location_id_to', $inventoryBalance->location_id);
        })
        ->orderByDesc('created_at')
        ->paginate(30);

        return view('inventory-balances.show', compact('inventoryBalance', 'movements'));
    }
}
