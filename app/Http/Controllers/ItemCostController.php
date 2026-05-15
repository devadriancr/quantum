<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemCost;
use Illuminate\Http\Request;

class ItemCostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $items = Item::whereHas('itemCosts')
            ->when(
                $search,
                fn($q) => $q
                    ->where('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
            )
            ->withCount('itemCosts')
            ->with('lastCost.currency')
            ->orderBy('code')
            ->paginate(10)
            ->withQueryString();

        return view('item-costs.index', compact('items', 'search'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        $costs = ItemCost::where('item_id', $item->id)
            ->with('currency')
            ->orderByDesc('start_date')
            ->paginate(10);

        return view('item-costs.show', compact('item', 'costs'));
    }
}
