<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::check() && (Auth::user()->isCustomer() || Auth::user()->isSeller()) && !Auth::user()->banned) {
            return $next($request);
        } else {
            return redirect()->guest(route('user.login'));
        }
    }
}
