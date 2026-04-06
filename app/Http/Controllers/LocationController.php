<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $locations = Location::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('code')
            ->paginate(10)
            ->withQueryString();

        return view('locations.index', compact('locations'));
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
            'code' => 'required|string|max:255|unique:locations,code',
            'name' => 'required|string|max:255',
            'warehouse_id' => 'required|exists:warehouses,id',
            'row' => 'nullable|integer|min:1',
            'rack' => 'nullable|integer|min:1',
            'shelf' => 'nullable|integer|min:1',
            'zone' => 'nullable|in:RECEIVING,STORAGE,SHIPPING',
            'available_capacity' => 'nullable|numeric|min:0',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->boolean('status');
        $validated['created_by_user_id'] = auth()->id();
        $validated['updated_by_user_id'] = auth()->id();

        $location = Location::create($validated);

        return redirect()->route('locations.show', $location)
            ->with('success', __('Ubicación creada correctamente.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        return view('locations.show', compact('location'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location)
    {
        return view('locations.edit', compact('location'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'row'   => 'nullable|integer|min:1',
            'rack'  => 'nullable|integer|min:1',
            'shelf' => 'nullable|integer|min:1',
            'zone'  => 'nullable|in:RECEIVING,STORAGE,SHIPPING',
        ]);

        $validated['updated_by_user_id'] = auth()->id();
        $location->update($validated);

        return redirect()->route('locations.show', $location)
            ->with('success', __('Ubicación actualizada correctamente.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
