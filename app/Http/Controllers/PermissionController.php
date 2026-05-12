<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $permissions = Permission::when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->withCount('roles')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('permissions.index', compact('permissions', 'search'));
    }

    public function create()
    {
        return view('permissions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:150', 'unique:permissions,name'],
            'label' => ['nullable', 'string', 'max:200'],
        ]);

        Permission::create($data);

        return redirect()->route('permissions.index')
            ->with('success', "Permiso \"{$data['name']}\" creado correctamente.");
    }

    public function edit(Permission $permission)
    {
        return view('permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:150', 'unique:permissions,name,' . $permission->id],
            'label' => ['nullable', 'string', 'max:200'],
        ]);

        $permission->update($data);

        return redirect()->route('permissions.index')
            ->with('success', "Permiso actualizado correctamente.");
    }

    public function destroy(Permission $permission)
    {
        if ($permission->roles()->count() > 0) {
            return back()->with('error', "No se puede eliminar el permiso \"{$permission->name}\" porque está asignado a uno o más roles.");
        }

        $name = $permission->name;
        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('success', "Permiso \"{$name}\" eliminado correctamente.");
    }
}
