<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\StockMovementLine;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $search    = $request->get('search');
        $dateRange = $request->get('date_range');

        [$dateFrom, $dateTo] = $this->parseDateRange($dateRange);

        $query = StockMovementLine::query()
            ->with([
                'item',
                'stockMovement.container',
                'stockMovement.transactionType',
                'stockMovement.locationFrom.warehouse',
                'stockMovement.locationTo.warehouse',
                'stockMovement.partner',
                'stockMovement.createdBy',
            ])
            ->join('stock_movements as sm', 'stock_movement_lines.stock_movement_id', '=', 'sm.id')
            ->leftJoin('items as it', 'stock_movement_lines.item_id', '=', 'it.id')
            ->select('stock_movement_lines.*');

        if ($search) {
            $like = '%' . $search . '%';
            $query->where(function ($q) use ($like) {
                $q->where('it.code', 'like', $like)
                    ->orWhere('it.description', 'like', $like)
                    ->orWhere('stock_movement_lines.serial_batch_number', 'like', $like)
                    ->orWhereHas('stockMovement.container', fn($c) => $c->where('code', 'like', $like));
            });
        }

        if ($request->filled('movement_type')) {
            $query->whereIn('sm.movement_type', (array) $request->movement_type);
        }

        if ($dateFrom && $dateTo) {
            $query->whereBetween('sm.movement_date', [$dateFrom, $dateTo]);
        }

        $lines = $query->orderByDesc('stock_movement_lines.created_at')
            ->paginate(10)
            ->withQueryString();

        return view('stock-movements.index', compact('lines', 'search', 'dateRange'));
    }

    private function parseDateRange(?string $dateRange): array
    {
        if (blank($dateRange) || ! str_contains($dateRange, ' - ')) {
            return [null, null];
        }

        [$fromStr, $toStr] = explode(' - ', $dateRange, 2);

        return [trim($fromStr), trim($toStr)];
    }
}
