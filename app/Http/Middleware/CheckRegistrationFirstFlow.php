<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRegistrationFirstFlow
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        /**
         * The 'customer_registration_verify' setting item toggles a "Verification First" registration flow for new customers.
         *
         * @see /.docs/business_settings/customer_registration_verify.md
         */
        if (get_setting('customer_registration_verify') == 1) {
            return redirect()->route('registration.verification');
        }

        return $next($request);
    }
}
