<?php

namespace App\Http\Middleware;

use App\Services\LocaleService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Unified locale middleware for both web and API requests.
 *
 * ## What is a "locale"?
 * A locale is a code that identifies both a language AND a region.
 * Examples: "en_US" = English in the United States, "ar_SA" = Arabic in Saudi Arabia.
 * The format is: {language}_{REGION}  (language is lowercase, a region is uppercase).
 *
 * ## Why do we separate a language from a region?
 * - The LANGUAGE part (e.g. "en", "ar") tells Laravel which translations to load.
 *   This is what App::setLocale() uses to pick the right translation strings.
 * - The REGION part (e.g. "US", "SA") is stored in session for use with
 *   things like currency formatting ($, ﷼), date format (MM/DD vs DD/MM), etc.
 *
 * ## How does it decide which locale to use?
 * It checks in this priority order (the first one wins):
 *  1. Request header — API clients send "App-Language: ar_SA" or browsers send "Accept-Language"
 *  2. Session — if the user previously selected a language, it's saved in their session
 *  3. Config fallback — uses config('app.locale') which defaults to "en_US"
 *
 * ## Where are supported locales defined?
 * In config/app.php under 'supported_locales'. Any locale not in that list is rejected,
 * and the middleware falls back to the default.
 */
class AppLocale
{
    /**
     * Handle an incoming request.
     *
     * This is the main method Laravel calls for every HTTP request.
     * It figures out the locale, sets it, and then lets the request continue.
     */
    public function handle(Request $request, Closure $next): Response
    {
        app(LocaleService::class)->setCurrentLocaleFromRequest($request);

        // Override Kenepa Translation Manager config at runtime.
        // - navigation_group: QuickTranslate hardcodes config() so we set it to
        //   the translated label to match our resources' getLocalizedLabel().
        // - quick_translate_navigation_registration: Hide Quick Translate from nav.
        //   (mergeConfigFrom gives vendor defaults priority, so the published
        //   config's `false` gets overwritten by the vendor's `true`.)
        config([
            'translation-manager.navigation_group' => __('admin/navigation.settings'),
        ]);

        return $next($request);
    }
}
