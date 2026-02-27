<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class IsCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check() && (Auth::user()->user_type == 'customer')) {
            return $next($request);
        } else {
            return redirect()->guest(route('user.login'));
        }
    }
}
