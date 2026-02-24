<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for the AppLocale middleware.
 *
 * Verifies locale detection order (header → session → fallback),
 * full locale parsing, region storage, and invalid locale rejection.
 */
class AppLocaleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the default locale is applied when no header or session is set.
     */
    public function test_default_locale_is_applied(): void
    {
        $response = $this->get('/');

        $this->assertContains(app()->getLocale(), ['en', 'ar']);
    }

    /**
     * Test that App-Language header sets the locale (API-style).
     */
    public function test_app_language_header_sets_locale(): void
    {
        $response = $this->withHeaders([
            'App-Language' => 'ar_SA',
        ])->get('/');

        $this->assertEquals('ar', app()->getLocale());
    }

    /**
     * Test that Accept-Language header sets the locale.
     */
    public function test_accept_language_header_sets_locale(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'ar-SA,ar;q=0.9,en-US;q=0.8',
        ])->get('/');

        $this->assertEquals('ar', app()->getLocale());
    }

    /**
     * Test that session locale persists the full locale code.
     */
    public function test_session_stores_full_locale(): void
    {
        $response = $this->withHeaders([
            'App-Language' => 'ar_SA',
        ])->get('/');

        $response->assertSessionHas('locale', 'ar_SA');
        $response->assertSessionHas('locale_region', 'SA');
    }

    /**
     * Test that session locale is used when no header is present.
     */
    public function test_session_locale_is_used_without_header(): void
    {
        // First request sets the session
        $this->withHeaders(['App-Language' => 'ar_SA'])->get('/');

        // Second request without header should use session
        $response = $this->get('/');

        $this->assertEquals('ar', app()->getLocale());
        $response->assertSessionHas('locale', 'ar_SA');
    }

    /**
     * Test that an invalid locale falls back to the default.
     */
    public function test_invalid_locale_falls_back_to_default(): void
    {
        $response = $this->withHeaders([
            'App-Language' => 'xx_ZZ',
        ])->get('/');

        // Should fall back to the default locale's language part
        $this->assertContains(app()->getLocale(), ['en', 'ar']);
    }

    /**
     * Test that language-only codes resolve to full locale.
     */
    public function test_language_only_resolves_to_full_locale(): void
    {
        $response = $this->withHeaders([
            'App-Language' => 'ar',
        ])->get('/');

        $this->assertEquals('ar', app()->getLocale());
        $response->assertSessionHas('locale', 'ar_SA');
    }

    /**
     * Test that hyphenated locales are normalised to underscores.
     */
    public function test_hyphenated_locales_are_normalised(): void
    {
        $response = $this->withHeaders([
            'App-Language' => 'en-US',
        ])->get('/');

        $this->assertEquals('en', app()->getLocale());
        $response->assertSessionHas('locale', 'en_US');
    }
}
