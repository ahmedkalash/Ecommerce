<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;

class IsUser
{
    public function handle(Request $request, Closure $next)
    {
        if (
            Auth::check() &&
            (Auth::user()->isCustomer() ||
                Auth::user()->isSeller() ||
                Auth::user()->isDeliveryBoy())
        ) {

            return $next($request);
        } else {
            return redirect()->guest(route('user.login'));
        }
    }
}
