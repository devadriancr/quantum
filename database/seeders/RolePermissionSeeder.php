<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roleNames = [
            'Administrator',
            'Manager',
            'Assistant Manager',
            'Supervisor',
            'Leader',
            'Team Leader',
            'Consigned Parts',
            'Internal Planning',
            'External Planning',
        ];

        foreach ($roleNames as $name) {
            Role::firstOrCreate(['name' => $name], ['label' => $name]);
        }

        $modules = [
            'items', 'item-types', 'item-classes', 'measurement-units',
            'warehouses', 'locations', 'partners', 'projects', 'project-prefixes',
            'packing-specifications', 'transaction-types', 'containers',
            'reception', 'stock-movements', 'inventory-balances', 'material-outputs',
            'stock-limits', 'currencies', 'item-costs', 'inventory-adjustments',
            'unit-plans', 'roles', 'permissions', 'users',
        ];

        $actions = ['create', 'view', 'edit', 'delete'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(
                    ['name' => "{$action} {$module}"],
                    ['label' => ucfirst($action) . ' ' . str_replace('-', ' ', $module)]
                );
            }
        }

        // Special permissions that don't follow the CRUD pattern
        $specialPermissions = [
            'approve inventory-adjustments' => 'Approve inventory adjustments',
            'refresh currencies'            => 'Refresh currency rates',
        ];

        foreach ($specialPermissions as $name => $label) {
            Permission::firstOrCreate(['name' => $name], ['label' => $label]);
        }

        // Assign ALL permissions to Administrator
        $adminRole = Role::where('name', 'Administrator')->first();
        $adminRole->permissions()->sync(Permission::all()->pluck('id'));

        // Assign the Administrator role to user ID 1 if not already assigned
        $user = User::find(1);
        if ($user && ! $user->role_id) {
            $user->update(['role_id' => $adminRole->id]);
        }
    }
}
