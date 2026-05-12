<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::before(function (User $user, string $ability) {
            // Administrator bypasses every gate check
            if ($user->role?->name === 'Administrator') {
                return true;
            }

            // For any other role, check the permission by name against the DB
            if ($user->role_id) {
                return $user->role?->permissions()->where('name', $ability)->exists();
            }

            // No role assigned → deny
            return false;
        });
    }
}

