<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * Routes protected by the 'admin' guard should redirect to the admin login page,
     * while all other unauthenticated requests go to the default customer login.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Check if the route uses the 'admin' guard
        if ($request->is('admin/*') || $request->is('admin')) {
            return route('admin.login');
        }

        return route('login');
    }
}
