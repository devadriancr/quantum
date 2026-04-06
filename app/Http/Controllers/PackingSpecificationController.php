<?php

namespace App\Http\Controllers;

use App\Models\PackingSpecification;
use Illuminate\Http\Request;

class PackingSpecificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = PackingSpecification::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $packingSpecifications = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('packing-specifications.index', compact('packingSpecifications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('packing-specifications.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'quantity' => 'nullable|integer|min:0',
            'status'   => 'boolean',
        ]);

        $validated['status'] = $request->boolean('status');

        PackingSpecification::create($validated);

        return redirect()->route('packing-specifications.index')
            ->with('success', __('Especificación de empaque creada correctamente.'));
    }
    /**
     * Display the specified resource.
     */
    public function show(PackingSpecification $packingSpecification)
    {
        return view('packing-specifications.show', compact('packingSpecification'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PackingSpecification $packingSpecification)
    {
        return view('packing-specifications.edit', compact('packingSpecification'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PackingSpecification $packingSpecification)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'quantity' => 'nullable|integer|min:0',
            'status'   => 'boolean',
        ]);

        $validated['status'] = $request->boolean('status');

        $packingSpecification->update($validated);

        return redirect()->route('packing-specifications.show', $packingSpecification)
            ->with('success', __('Especificación de empaque actualizada correctamente.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PackingSpecification $packingSpecification)
    {
        $packingSpecification->delete();

        return redirect()->route('packing-specifications.index')
            ->with('success', __('Especificación de empaque eliminada correctamente.'));
    }
}
