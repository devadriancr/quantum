<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Location;
use App\Models\StockLimit;
use Illuminate\Http\Request;

class StockLimitController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $limits = StockLimit::with(['item', 'location.warehouse'])
            ->where('active', true)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->whereHas(
                        'item',
                        fn($i) => $i
                            ->where('code', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%")
                    )->orWhereHas(
                        'location',
                        fn($l) => $l
                            ->where('code', 'like', "%{$search}%")
                    );
                });
            })
            ->orderBy('item_id')
            ->paginate(10)
            ->withQueryString();

        return view('stock-limits.index', compact('limits', 'search'));
    }

    public function create()
    {
        $items     = Item::where('active', true)->orderBy('code')->get(['id', 'code', 'description']);
        $locations = Location::with('warehouse')->orderBy('code')->get(['id', 'code', 'name', 'warehouse_id']);

        return view('stock-limits.create', compact('items', 'locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id'          => 'required|exists:items,id',
            'location_id'      => 'required|exists:locations,id',
            'minimum_quantity' => 'required|numeric|min:0',
            'maximum_quantity' => 'required|numeric|min:0|gte:minimum_quantity',
            'reorder_point'    => 'required|numeric|min:0',
            'reorder_quantity' => 'required|numeric|min:0',
            'active'           => 'boolean',
        ], [
            'item_id.required'          => 'Selecciona un número de parte.',
            'location_id.required'      => 'Selecciona una ubicación.',
            'maximum_quantity.gte'      => 'La cantidad máxima debe ser mayor o igual al mínimo.',
            'minimum_quantity.required' => 'El mínimo es obligatorio.',
            'maximum_quantity.required' => 'El máximo es obligatorio.',
            'reorder_point.required'    => 'El punto de reorden es obligatorio.',
            'reorder_quantity.required' => 'La cantidad a pedir es obligatoria.',
        ]);

        $validated['active']              = $request->boolean('active', true);
        $validated['created_by_user_id']  = auth()->id();
        $validated['updated_by_user_id']  = auth()->id();

        $limit = StockLimit::create($validated);

        return redirect()
            ->route('stock-limits.index')
            ->with('success', 'Límite de stock creado correctamente.');
    }

    public function edit(StockLimit $stockLimit)
    {
        $items     = Item::where('active', true)->orderBy('code')->get(['id', 'code', 'description']);
        $locations = Location::with('warehouse')->orderBy('code')->get(['id', 'code', 'name', 'warehouse_id']);

        return view('stock-limits.edit', compact('stockLimit', 'items', 'locations'));
    }

    public function update(Request $request, StockLimit $stockLimit)
    {
        $validated = $request->validate([
            'minimum_quantity' => 'required|numeric|min:0',
            'maximum_quantity' => 'required|numeric|min:0|gte:minimum_quantity',
            'reorder_point'    => 'required|numeric|min:0',
            'reorder_quantity' => 'required|numeric|min:0',
            'active'           => 'boolean',
        ], [
            'maximum_quantity.gte'      => 'La cantidad máxima debe ser mayor o igual al mínimo.',
            'minimum_quantity.required' => 'El mínimo es obligatorio.',
            'maximum_quantity.required' => 'El máximo es obligatorio.',
            'reorder_point.required'    => 'El punto de reorden es obligatorio.',
            'reorder_quantity.required' => 'La cantidad a pedir es obligatoria.',
        ]);

        $validated['active']             = $request->boolean('active');
        $validated['updated_by_user_id'] = auth()->id();

        $stockLimit->update($validated);

        return redirect()
            ->route('stock-limits.index')
            ->with('success', 'Límite de stock actualizado correctamente.');
    }

    public function destroy(StockLimit $stockLimit)
    {
        $stockLimit->delete();

        return redirect()
            ->route('stock-limits.index')
            ->with('success', 'Límite de stock eliminado correctamente.');
    }
}
