<?php

namespace App\Http\Middleware;

use App;
use Carbon\Carbon;
use Closure;
use Session;

class Language
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Session::has('locale')) {
            $locale = Session::get('locale');
        } else {
            $locale = env('DEFAULT_LANGUAGE', 'en');
        }

        App::setLocale($locale);
        $request->session()->put('locale', $locale);

        $langcode = Session::has('langcode') ? Session::get('langcode') : 'en';
        Carbon::setLocale($langcode);

        return $next($request);
    }
}
