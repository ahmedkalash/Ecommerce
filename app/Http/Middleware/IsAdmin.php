<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * Ensures only admin and staff users can access admin routes.
     * Banned users are logged out and redirected.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // Strictly use admin guard only
        $user = Auth::guard('admin')->user();

        if (!$user) {
            return redirect()->route('admin.login');
        }

        // ⚠️ CRITICAL SECURITY CHECK - DO NOT REMOVE
        // This check is NOT redundant even though we use the Admin model with global scope.
        //
        // Why this is necessary:
        // 1. Global scopes only apply to database QUERIES (Admin::where(), etc.)
        // 2. Auth::guard('admin')->user() retrieves from SESSION, bypassing the global scope
        // 3. Protects against:
        //    - Manual login bypass: Auth::guard('admin')->login($customerUser)
        //    - Stale sessions: User's type changed after login but session still valid
        //    - Session tampering/hijacking attacks
        //
        // This is defense-in-depth: Global scope protects query layer, this protects request layer.
        if (!in_array($user->user_type, ['admin', 'staff'])) {
            abort(403, 'Unauthorized action.');
        }

        // Handle banned users with a logout and flash message
        if ($user->banned) {
            Auth::guard('admin')->logout();
            flash(translate('Your account has been banned'))->error();

            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
