<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::check() && (Auth::user()->isCustomer() || Auth::user()->isSeller()) && !Auth::user()->banned) {
            return $next($request);
        } else {
            session(['link' => url()->current()]);

            return redirect()->route('user.login');
        }
    }
}
