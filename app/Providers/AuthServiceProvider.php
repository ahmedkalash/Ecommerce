<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

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

        // Implicitly grant "admin" users all permissions
        Gate::before(function (User $user, $ability) {
            return $user->isSuperAdmin() ? true : null;
        });

        // Define the gate for accessing the admin panel
        // This centralizes the logic used in IsAdmin middleware
        Gate::define('access-admin-panel', function ($user) {
            return in_array($user->user_type, ['admin', 'staff']) && ! $user->banned;
        });
    }
}
