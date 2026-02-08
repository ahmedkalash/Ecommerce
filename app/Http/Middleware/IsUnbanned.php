<?php

namespace App\Http\Middleware;

use Closure;

class IsUnbanned
{
    public function handle($request, Closure $next)
    {
        if (auth()->check() && auth()->user()->banned) {
            auth()->logout();

            flash(translate('You are banned'));

            return redirect()->route('user.login');
        }

        return $next($request);
    }
}
