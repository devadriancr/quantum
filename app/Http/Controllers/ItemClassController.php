<?php

namespace App\Http\Controllers;

use App\Models\ItemClass;
use Illuminate\Http\Request;

class ItemClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $itemClasses = ItemClass::orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('item-classes.index', compact('itemClasses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:item_classes,code',
            'name' => 'required|string|max:255',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->boolean('status');
        $validated['created_by_user_id'] = auth()->id();
        $validated['updated_by_user_id'] = auth()->id();

        ItemClass::create($validated);

        return redirect()->route('item-classes.index')->with('success', 'Item Class created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $itemClass = ItemClass::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:item_classes,code,' . $id,
            'name' => 'required|string|max:255',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->boolean('status');
        $validated['updated_by_user_id'] = auth()->id();

        $itemClass->update($validated);

        return redirect()->route('item-classes.index')->with('success', 'Item Class updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
