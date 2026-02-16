<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HasNotVerifiedEmail
{
    /**
     * Handle an incoming request.
     * */
    public function handle(Request $request, Closure $next): mixed
    {
        // If a user is logged in and already verified, redirect to home
        if (($user = $request->user()) && $request->user()->hasVerifiedEmail()) {
            return redirect($user->homePage());
        }

        return $next($request);
    }
}
