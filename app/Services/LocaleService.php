<?php

namespace App\Services;

use App\DTOs\LocaleDTO;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocaleService
{
    /**
     * @param  string  $fullLocale  full locale format like 'en-US', 'en_US'
     */
    public function setCurrentLocale(string $fullLocale): void
    {
        $fullLocale = $this->validated($fullLocale);

        // Step 2: Extract language="ar" and region="SA" into a DTO
        $localeDto = LocaleDTO::fromString($fullLocale);

        // Step 3: Tell Laravel and Carbon to use the language for translations & date formatting
        // After this, __('ui.hello') will look up the Arabic translation if $language is "ar"
        App::setLocale($localeDto->language);
        Carbon::setLocale($fullLocale);  // Makes Carbon dates like "منذ 5 دقائق" instead of "5 minutes ago"

        // Step 4: Save the full locale and region in the session so we remember
        // the user's choice across page loads (and for future region-based features)
        Session::put('locale', $fullLocale);       // e.g. "ar_SA"
        Session::put('locale_region', $localeDto->region);     // e.g. "SA"
    }

    /**
     * Extract the primary locale from an Accept-Language header value.
     *
     * Browsers send a complex header like: "ar-SA,ar;q=0.9,en-US;q=0.8"
     * This means: "I prefer ar-SA, then ar, then en-US"
     * The ";q=0.9" part is a quality/preference score (0 to 1).
     *
     * We only care about the FIRST (highest priority) locale, so we:
     * 1. Split by comma → get "ar-SA" (the first/preferred one)
     * 2. Split by semicolon → remove the quality score part
     * 3. Trim whitespace → clean result
     *
     * @param  string  $header  Raw Accept-Language header value
     * @return string First preferred locale (e.g. "ar-SA")
     */
    private function parseAcceptLanguage(string $header): string
    {
        $primary = explode(',', $header)[0];    // "ar-SA,ar;q=0.9" → "ar-SA"
        $primary = explode(';', $primary)[0];   // "ar-SA;q=1.0"    → "ar-SA"

        return trim($primary);
    }

    /**
     * Validate and normalize a locale string against our supported locales.
     *
     * This method ensures we only accept locales we've configured in config/app.php.
     * It handles multiple input formats:
     *   - Full locale with underscore: "en_US" ✓
     *   - Full locale with hyphen (BCP 47 standard): "en-US" → normalized to "en_US"
     *   - Language-only: "ar" → resolved to "ar_SA" (first matching supported locale)
     *   - Unsupported: "xx_ZZ" → falls back to config default
     *
     * @param  string  $locale  The raw locale string to validate
     * @return string A validated, normalized locale from the supported list
     */
    private function validated(string $locale): string
    {
        // Normalize: browsers use hyphens (BCP 47: "en-US"), but Laravel/PHP
        // convention uses underscores (POSIX: "en_US"). Convert to underscore.
        $locale = str_replace('-', '_', trim($locale));

        // Get the list of supported locale codes from config/app.php
        // array_keys() gives us just the keys: ["en_US", "ar_SA"]
        $supported = array_keys(static::getSupportedLocales());

        // Best case: exact match (e.g. user sent "en_US" and we support "en_US")
        if (in_array($locale, $supported, true)) {
            return $locale;
        }

        // Partial match: user sent just "ar" (language only, no region)
        // We look for the first supported locale that has the same language part
        // So "ar" matches "ar_SA" because ar_SA's language part is "ar"
        $language = LocaleDTO::fromString($locale)->language;

        foreach ($supported as $supportedLocale) {
            if (LocaleDTO::fromString($supportedLocale)->language === $language) {
                return $supportedLocale;  // "ar" → "ar_SA"
            }
        }

        // Nothing matched — fall back to the app's default locale
        return config('app.full_locale', 'en_US');
    }

    /**
     * Determine the locale from the request using the priority chain:
     * header → session → config fallback.
     *
     * Think of this as a series of "if" checks — it tries each source
     * in order and uses the first one that has a value.
     */
    private function resolveLocale(Request $request): string
    {
        // Priority 1: Check for explicit header from API clients
        if ($request->hasHeader('App-Language')) {
            $candidate = $request->header('App-Language');

            // Priority 2: Kenepa Translation Manager language switcher
            // Kenepa stores the selected language under the 'language' key in session.
            // We check this BEFORE the Accept-Language browser header so a manual
            // language selection always wins over the browser's preference.
        } elseif ($request->hasSession() && Session::has('language')) {
            $candidate = Session::get('language');

            // Priority 3: Our own session 'locale' key (e.g. set by other parts of the app)
        } elseif ($request->hasSession() && Session::has('locale')) {
            $candidate = Session::get('locale');

            // Priority 4: Browser Accept-Language header
        } elseif ($request->hasHeader('Accept-Language')) {
            $candidate = $this->parseAcceptLanguage($request->header('Accept-Language'));

            // Priority 5: Config fallback
        } else {
            $candidate = config('app.locale', 'en');
        }

        return $this->validated($candidate);
    }

    public function setCurrentLocaleFromRequest(Request $request): void
    {
        $fullLocale = $this->resolveLocale($request);
        $this->setCurrentLocale($fullLocale);
    }

    /**
     * Get current full locale lang code like 'en_US', 'ar_SA'.
     */
    public static function getCurrentFullLocale(): string
    {
        return session('locale', 'en_US');
    }

    /**
     * Get current locale lang code like 'en', 'ar'.
     */
    public static function getCurrentLocale(): string
    {
        return App::getLocale();
    }

    public static function getCurrentLocaleDir(): string
    {
        $current_locale = static::getCurrentFullLocale();

        return static::getSupportedLocales()[$current_locale]['dir'];
    }

    public static function getFullLocaleFlag(string $fullLocale): string
    {
        return asset(static::getSupportedLocales()[$fullLocale]['flag']);
    }

    public static function getCurrentLocaleLangCode(): string
    {
        $current_locale = static::getCurrentFullLocale();

        return static::getSupportedLocales()[$current_locale]['language'];
    }

    public static function getCurrentLocaleRegion(): string
    {
        $current_locale = static::getCurrentFullLocale();

        return static::getSupportedLocales()[$current_locale]['region'];
    }

    public static function getCurrentLocaleName(): string
    {
        $current_locale = static::getCurrentFullLocale();

        return static::getSupportedLocales()[$current_locale]['name'];
    }

    /**
     * @return array supported locales array
     *               e.g. ['en_US' => ['language' => 'en', 'region' => 'US', 'name' => 'English (US)', 'dir' => 'ltr', 'flag' => 'assets/images/country/ksa.png'],]
     */
    public static function getSupportedLocales(): array
    {
        return config('app.supported_locales', []);

    }
}
