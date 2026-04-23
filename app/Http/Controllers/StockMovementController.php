<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\StockMovementLine;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovementLine::query()
            ->with(['item', 'stockMovement.container', 'stockMovement.transactionType', 'stockMovement.locationTo', 'stockMovement.partner'])
            ->join('stock_movements as sm', 'stock_movement_lines.stock_movement_id', '=', 'sm.id')
            ->leftJoin('items as it', 'stock_movement_lines.item_id', '=', 'it.id')
            ->select('stock_movement_lines.*');

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('it.code', 'like', $search)
                  ->orWhere('it.description', 'like', $search)
                  ->orWhere('stock_movement_lines.serial_batch_number', 'like', $search)
                  ->orWhereHas('stockMovement.container', fn($c) => $c->where('code', 'like', $search));
            });
        }

        if ($request->filled('movement_type')) {
            $query->whereIn('sm.movement_type', (array) $request->movement_type);
        }

        if ($request->filled('status')) {
            $query->whereIn('sm.status', (array) $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('sm.movement_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('sm.movement_date', '<=', $request->date_to);
        }

        $lines = $query->orderByDesc('sm.movement_date')
            ->orderByDesc('sm.movement_time')
            ->paginate(10)
            ->withQueryString();

        return view('stock-movements.index', compact('lines'));
    }

    public function show(StockMovement $stockMovement)
    {
        $stockMovement->load([
            'transactionType',
            'locationFrom',
            'locationTo',
            'partner',
            'container',
            'shipmentDocument',
            'lines.item',
            'verifiedBy',
            'createdBy',
        ]);

        $totalQty  = $stockMovement->lines->sum('quantity_received');
        $totalCost = $stockMovement->lines->sum(fn($l) => $l->quantity_received * ($l->unit_cost ?? 0));

        return view('stock-movements.show', compact('stockMovement', 'totalQty', 'totalCost'));
    }
}
