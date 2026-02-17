<?php

namespace App\Http\Middleware;

use App\Models\BusinessSetting;
use Auth;
use Closure;

class CheckoutMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (BusinessSetting::where('type', 'guest_checkout_active')->first()->value != 1) {
            if (Auth::check()) {
                return $next($request);
            } else {
                return redirect()->guest(route('user.login'));
            }
        } else {
            return $next($request);
        }
    }
}
