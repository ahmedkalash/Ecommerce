<?php

namespace App\Http\Middleware;

use Closure;

class IsUnBanned
{
    public function handle($request, Closure $next)
    {
        if (auth()->check() && auth()->user()->isBanned()) {
            auth()->logout();

            flash(__('auth.banned'))->error();

            return redirect()->route('user.login');
        }

        return $next($request);
    }
}
