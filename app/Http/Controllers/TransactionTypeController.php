<?php

namespace App\Http\Controllers;

use App\Models\TransactionType;
use Illuminate\Http\Request;

class TransactionTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TransactionType::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $transactionTypes = $query->orderBy('code')->paginate(15)->withQueryString();

        return view('transaction-types.index', compact('transactionTypes'));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TransactionType $transactionType)
    {
        return view('transaction-types.show', compact('transactionType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransactionType $transactionType)
    {
        return view('transaction-types.edit', compact('transactionType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransactionType $transactionType)
    {
        $validated = $request->validate([
            'transaction_category' => 'nullable|in:RECEIPT,SHIPMENT,INTERNAL_TRANSFER,ADJUSTMENT,RETURN,OTHER',
            'affects_inventory'    => 'boolean',
            'direction'            => 'nullable|in:IN,OUT',
            'status'               => 'required|in:ACTIVE,INACTIVE',
        ]);

        $validated['affects_inventory'] = $request->boolean('affects_inventory');

        $transactionType->update($validated);

        return redirect()->route('transaction-types.show', $transactionType)
            ->with('success', __('Tipo de transacción actualizado correctamente.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionType $transactionType)
    {
        //
    }
}
