<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /***
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     *
     */
    public function index(Request $request)
    {
        $query = Item::with(['itemClass', 'itemType', 'measurementUnit', 'packingSpecification']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('code')->paginate(15)->withQueryString();

        return view('items.index', compact('items'));
    }

    /***
     * Display the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        $item->load(['itemClass', 'itemType', 'measurementUnit', 'packingSpecification']);

        return view('items.show', compact('item'));
    }

    /***
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    /***
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'default_safety_stock' => 'nullable|integer|min:0',
            'last_unit_cost'       => 'nullable|numeric|min:0',
        ]);

        $item->update($validated);

        return redirect()->route('items.show', $item)
            ->with('success', __('Artículo actualizado correctamente.'));
    }
}
