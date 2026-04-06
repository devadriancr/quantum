<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $query = Partner::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $partners = $query->orderBy('code')->paginate(10)->withQueryString();

        return view('partners.index', compact('partners'));
    }

    public function create()
    {
        return view('partners.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'          => 'required|string|max:255|unique:partners,code',
            'name'          => 'required|string|max:255',
            'partner_type'  => 'required|in:supplier,customer,both',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'address'       => 'nullable|string|max:255',
            'city'          => 'nullable|string|max:100',
            'country'       => 'nullable|string|max:100',
            'status'        => 'required|in:ACTIVE,INACTIVE',
        ]);

        Partner::create($data);

        return redirect()->route('partners.index')
            ->with('success', __('Socio creado correctamente.'));
    }

    public function show(Partner $partner)
    {
        return view('partners.show', compact('partner'));
    }

    public function edit(Partner $partner)
    {
        return view('partners.edit', compact('partner'));
    }

    public function update(Request $request, Partner $partner)
    {
        $data = $request->validate([
            'code'          => 'required|string|max:255|unique:partners,code,' . $partner->id,
            'name'          => 'required|string|max:255',
            'partner_type'  => 'required|in:supplier,customer,both',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'address'       => 'nullable|string|max:255',
            'city'          => 'nullable|string|max:100',
            'country'       => 'nullable|string|max:100',
            'status'        => 'required|in:ACTIVE,INACTIVE',
        ]);

        $partner->update($data);

        return redirect()->route('partners.show', $partner)
            ->with('success', __('Socio actualizado correctamente.'));
    }

    public function destroy(Partner $partner)
    {
        $partner->delete();

        return redirect()->route('partners.index')
            ->with('success', __('Socio eliminado correctamente.'));
    }
}
