<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\PermissionRegistrar;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        $this->implicitlyGrantAdminAllPermissions();

        $this->registerDynamicPermissionsGate();

        $this->registerAccessAdminPanel();
    }

    private function implicitlyGrantAdminAllPermissions(): void
    {
        // Implicitly grant "admin" users all permissions
        Gate::before(function (User $user, $ability) {
            if ($user->isSuperAdmin()) {
                return true;
            }

            return null;
        });
    }

    private function registerDynamicPermissionsGate(): void
    {
        // Dynamic Gate to bypass the need for individual Policy classes
        Gate::before(function (User $user, $ability, $models) {
            // Ignored standard Laravel abilities or custom gates that shouldn't be verified against Spatie
            $ignoredAbilities = ['access-admin-panel', 'use-translation-manager'];
            if (in_array($ability, $ignoredAbilities)) {
                return null; // Fall through to explicitly defined gates
            }

            // Get the model class or instance
            $model = $models[0] ?? null;
            $modelName = class_basename($model);

            // The default expected Spatie permission is the raw ability (e.g., 'page_Dashboard')
            $permissionName = $ability;

            if ($modelName) {
                // If it's a model check, we map 'viewAny' to 'view_any_coupon'
                $snakeAbility = Str::snake($ability);
                $snakeModel = Str::snake($modelName);
                $permissionName = "{$snakeAbility}_{$snakeModel}";
            }

            $registrar = app(PermissionRegistrar::class);
            $permissions = $registrar->getPermissions();

            // Check if the primary targeted permission exists in the database
            $primaryExists = $permissions->where('name', $permissionName)->where('guard_name', 'admin')->isNotEmpty();
            // Check if the raw fallback ability exists in the database (useful if standard names were overridden)
            $fallbackExists = $permissions->where('name', $ability)->where('guard_name', 'admin')->isNotEmpty();

            // If NEITHER specifically mapped nor fallback standard permission exists,
            // we throw an error rather than falling back.
            if (! $primaryExists && ! $fallbackExists) {
                throw new PermissionDoesNotExist(
                    "Neither custom permission '{$permissionName}' nor standard permission '{$ability}' exists."
                );
            }

            // If we are here, at least one of them exists. Let's check if the user has either.
            if ($primaryExists && method_exists($user, 'hasPermissionTo') && $user->hasPermissionTo(
                $permissionName,
                'admin'
            )) {
                return true;
            }

            if ($fallbackExists && method_exists($user, 'hasPermissionTo') && $user->hasPermissionTo(
                $ability,
                'admin'
            )) {
                return true;
            }

            // Both checks failed: the user does not have the permissions.
            return false;
        });
    }

    private function registerAccessAdminPanel(): void
    {
        // Define the gate for accessing the admin panel
        // This centralizes the logic used in IsAdmin middleware
        Gate::define('access-admin-panel', function ($user) {
            return in_array($user->user_type, ['admin', 'staff']) && ! $user->banned;
        });

        // Gate required by kenepa/translation-manager Filament plugin
        Gate::define('use-translation-manager', function ($user) {
            return in_array($user->user_type, ['admin', 'staff']) && ! $user->banned;
        });
    }
}
