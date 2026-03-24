<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemType;

class ItemTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $itemTypes = ItemType::all();
        return view('item-types.index', compact('itemTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('item-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:item_types',
            'name' => 'required|string',
            'status' => 'boolean',
        ]);

        ItemType::create($request->all());

        return redirect()->route('item-types.index')->with('success', 'Item Type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $itemType = ItemType::findOrFail($id);
        return view('item-types.show', compact('itemType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $itemType = ItemType::findOrFail($id);
        return view('item-types.edit', compact('itemType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $itemType = ItemType::findOrFail($id);

        $request->validate([
            'code' => 'required|string|unique:item_types,code,' . $id,
            'name' => 'required|string',
            'status' => 'boolean',
        ]);

        $itemType->update($request->all());

        return redirect()->route('item-types.index')->with('success', 'Item Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $itemType = ItemType::findOrFail($id);
        $itemType->delete();

        return redirect()->route('item-types.index')->with('success', 'Item Type deleted successfully.');
    }
}
