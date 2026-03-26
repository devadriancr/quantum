<?php

namespace App\Http\Controllers;

use App\Models\MeasurementUnit;
use Illuminate\Http\Request;

class MeasurementUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $measurementUnits = MeasurementUnit::orderBy('name')->paginate(10)->withQueryString();

        return view('measurement-units.index', compact('measurementUnits'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('measurement-units.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:measurement_units,code',
            'name' => 'required|string|max:255',
            'status' => 'boolean',
        ]);

        $validated['status'] = $request->boolean('status');

        MeasurementUnit::create($validated);

        return redirect()->route('measurement-units.index')
            ->with('success', __('Unidad de medida creada exitosamente.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(MeasurementUnit $measurementUnit)
    {
        return view('measurement-units.show', compact('measurementUnit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MeasurementUnit $measurementUnit)
    {
        return view('measurement-units.edit', compact('measurementUnit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MeasurementUnit $measurementUnit)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:measurement_units,code,' . $measurementUnit->id,
            'name' => 'required|string|max:255',
            'status' => 'boolean',
        ]);

        $validated['status'] = $request->boolean('status');

        $measurementUnit->update($validated);

        return redirect()->route('measurement-units.index')
            ->with('success', __('Unidad de medida actualizada exitosamente.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MeasurementUnit $measurementUnit)
    {
        $measurementUnit->delete();

        return redirect()->route('measurement-units.index')
            ->with('success', __('Unidad de medida eliminada exitosamente.'));
    }
}
