<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('permissions')->orderBy('name')->paginate(15);

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return explode(' ', $p->name, 2)[1] ?? $p->name;
        });

        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:100', 'unique:roles,name'],
            'label'          => ['nullable', 'string', 'max:150'],
            'permissions'    => ['nullable', 'array'],
            'permissions.*'  => ['exists:permissions,id'],
        ]);

        $role = Role::create([
            'name'  => $data['name'],
            'label' => $data['label'] ?? null,
        ]);

        if (! empty($data['permissions'])) {
            $role->permissions()->sync($data['permissions']);
        }

        return redirect()->route('roles.index')
            ->with('success', "Rol \"{$role->name}\" creado correctamente.");
    }

    public function show(Role $role)
    {
        $role->load('permissions');

        $modules = [
            'items', 'item-types', 'item-classes', 'measurement-units',
            'warehouses', 'locations', 'partners', 'projects', 'project-prefixes',
            'packing-specifications', 'transaction-types', 'containers',
            'reception', 'stock-movements', 'inventory-balances', 'material-outputs',
            'stock-limits', 'roles', 'permissions', 'users',
        ];

        $assigned = $role->permissions->pluck('name')->toArray();

        return view('roles.show', compact('role', 'modules', 'assigned'));
    }

    public function edit(Role $role)
    {
        $role->load('permissions');

        $permissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return explode(' ', $p->name, 2)[1] ?? $p->name;
        });

        $assignedIds = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'assignedIds'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:100', 'unique:roles,name,' . $role->id],
            'label'          => ['nullable', 'string', 'max:150'],
            'permissions'    => ['nullable', 'array'],
            'permissions.*'  => ['exists:permissions,id'],
        ]);

        $role->update([
            'name'  => $data['name'],
            'label' => $data['label'] ?? null,
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()->route('roles.index')
            ->with('success', "Rol \"{$role->name}\" actualizado correctamente.");
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return back()->with('error', "No se puede eliminar el rol \"{$role->name}\" porque hay usuarios asignados.");
        }

        $name = $role->name;
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', "Rol \"{$name}\" eliminado correctamente.");
    }
}
