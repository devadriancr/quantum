<?php

namespace App\Http\Controllers;

use App\Models\InventoryAdjustment;
use App\Models\InventoryAdjustmentLine;
use App\Models\InventoryBalance;
use App\Models\Item;
use App\Models\Location;
use App\Models\StockMovement;
use App\Models\StockMovementLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $adjustments = InventoryAdjustment::with(['createdBy', 'lines'])
            ->when(
                $search,
                fn($q) => $q
                    ->where('adjustment_number', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")
            )
            ->orderByDesc('adjustment_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('inventory-adjustments.index', compact('adjustments', 'search'));
    }

    public function create()
    {

        $items = Item::where('active', true)->whereHas('itemClass', function ($query) {
            $query->where('code', 'S1');
        })->orderBy('code')->get(['id', 'code', 'description']);
        $locations = Location::with('warehouse')->where('status', 'AVAILABLE')->orderBy('code')->get(['id', 'code', 'name', 'warehouse_id']);
        $types     = InventoryAdjustment::ADJUSTMENT_TYPES;

        return view('inventory-adjustments.create', compact('items', 'locations', 'types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'adjustment_type'          => 'required|in:VARIANCE,DAMAGE,LOSS,FOUND,CORRECTION',
            'adjustment_date'          => 'required|date',
            'reason'                   => 'required|string|max:1000',
            'notes'                    => 'nullable|string|max:1000',
            'lines'                    => 'required|array|min:1',
            'lines.*.item_id'          => 'required|exists:items,id',
            'lines.*.location_id'      => 'required|exists:locations,id',
            'lines.*.adjusted_quantity' => 'required|numeric',
        ], [
            'lines.required'                      => 'Debe agregar al menos una línea de ajuste.',
            'lines.*.item_id.required'            => 'Selecciona un número de parte en cada línea.',
            'lines.*.location_id.required'        => 'Selecciona una ubicación en cada línea.',
            'lines.*.adjusted_quantity.required'  => 'La cantidad ajustada es obligatoria.',
            'lines.*.adjusted_quantity.numeric'   => 'La cantidad ajustada debe ser un número.',
        ]);

        $userId = Auth::id();

        DB::transaction(function () use ($request, $userId) {
            $number = 'ADJ-' . now()->format('Ymd-His');
            $lineCount = count($request->lines);

            $adjustment = InventoryAdjustment::create([
                'adjustment_number'  => $number,
                'adjustment_date'    => $request->adjustment_date,
                'adjustment_type'    => $request->adjustment_type,
                'reason'             => $request->reason,
                'notes'              => $request->notes,
                'status'             => 'APPLIED',
                'created_by_user_id' => $userId,
                'updated_by_user_id' => $userId,
            ]);

            foreach ($request->lines as $index => $lineData) {
                $balance = InventoryBalance::firstOrCreate(
                    [
                        'item_id'     => $lineData['item_id'],
                        'location_id' => $lineData['location_id'],
                    ],
                    [
                        'opening_quantity'   => 0,
                        'current_quantity'   => 0,
                        'reserved_quantity'  => 0,
                        'created_by_user_id' => $userId,
                        'updated_by_user_id' => $userId,
                    ]
                );

                $systemQty   = (float) $balance->current_quantity;
                $delta       = (float) $lineData['adjusted_quantity']; // lo que el usuario ingresa: cuánto sumar/restar
                $adjustedQty = $systemQty + $delta;                    // nueva cantidad total
                $variance    = $delta;                                 // el movimiento registra el delta

                InventoryAdjustmentLine::create([
                    'inventory_adjustment_id' => $adjustment->id,
                    'line_number'             => $index + 1,
                    'item_id'                 => $lineData['item_id'],
                    'location_id'             => $lineData['location_id'],
                    'system_quantity'         => $systemQty,
                    'adjusted_quantity'       => $adjustedQty,
                    'notes'                   => $lineData['notes'] ?? null,
                ]);

                // Un StockMovement por línea con su ubicación correcta
                $movNumber = $lineCount > 1 ? "{$number}-L" . ($index + 1) : $number;

                $stockMovement = StockMovement::create([
                    'movement_number'    => $movNumber,
                    'movement_date'      => $request->adjustment_date,
                    'movement_time'      => now()->toTimeString(),
                    'movement_type'      => 'ADJUSTMENT',
                    'location_id_to'     => $lineData['location_id'],
                    'status'             => 'COMPLETED',
                    'notes'              => "[{$number}] " . $request->reason,
                    'created_by_user_id' => $userId,
                    'updated_by_user_id' => $userId,
                ]);

                StockMovementLine::create([
                    'stock_movement_id'  => $stockMovement->id,
                    'item_id'            => $lineData['item_id'],
                    'quantity_received'  => $variance,
                    'notes'              => $lineData['notes'] ?? null,
                    'created_by_user_id' => $userId,
                    'updated_by_user_id' => $userId,
                ]);

                $balance->update([
                    'current_quantity'   => $adjustedQty,
                    'last_movement_id'   => $stockMovement->id,
                    'last_movement_date' => now(),
                    'updated_by_user_id' => $userId,
                ]);
            }
        });

        return redirect()
            ->route('inventory-adjustments.index')
            ->with('success', 'Ajuste de inventario registrado correctamente.');
    }

    public function show(InventoryAdjustment $inventoryAdjustment)
    {
        $inventoryAdjustment->load(['lines.item', 'lines.location.warehouse', 'createdBy']);

        return view('inventory-adjustments.show', compact('inventoryAdjustment'));
    }
}
