<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\TranslationLoader\LanguageLine;
use Tests\TestCase;

/**
 * Feature tests for the new Spatie-based translation system.
 *
 * Verifies that:
 * - The language_lines table exists and is writable
 * - Spatie's LanguageLine model stores and retrieves translations
 * - Laravel's __() helper loads translations from the database
 * - The translate() backward-compatible wrapper works correctly
 * - Missing keys fall back gracefully
 */
class TranslationSystemTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test that a LanguageLine can be created and persisted.
     */
    public function test_can_create_language_line(): void
    {
        $line = LanguageLine::create([
            'group' => 'ui',
            'key' => 'welcome_message',
            'text' => ['en' => 'Welcome!', 'ar' => 'مرحباً!'],
        ]);

        $this->assertDatabaseHas('language_lines', [
            'group' => 'ui',
            'key' => 'welcome_message',
        ]);

        $this->assertEquals('Welcome!', $line->getTranslation('en'));
        $this->assertEquals('مرحباً!', $line->getTranslation('ar'));
    }

    /**
     * Test that Laravel's __() helper resolves database translations.
     */
    public function test_laravel_double_underscore_loads_db_translations(): void
    {
        LanguageLine::create([
            'group' => 'ui',
            'key' => 'hello_world',
            'text' => ['en' => 'Hello World'],
        ]);

        // Flush the translator cache so the new line is picked up
        app('translator')->setLoaded([]);

        app()->setLocale('en');

        $this->assertEquals('Hello World', __('ui.hello_world'));
    }

    /**
     * Test that the translate() wrapper function delegates to __().
     */
    public function test_translate_helper_delegates_to_laravel(): void
    {
        LanguageLine::create([
            'group' => 'ui',
            'key' => 'dashboard',
            'text' => ['en' => 'Dashboard'],
        ]);

        app('translator')->setLoaded([]);
        app()->setLocale('en');

        // translate() normalises the key to snake_case and prepends group
        $result = translate('Dashboard');
        $this->assertEquals('Dashboard', $result);
    }

    /**
     * Test that missing translation keys fall back to the original key.
     */
    public function test_missing_key_falls_back_to_original(): void
    {
        app()->setLocale('en');

        $result = translate('This Key Does Not Exist');
        $this->assertEquals('This Key Does Not Exist', $result);
    }

    /**
     * Test that the translate() helper respects locale override.
     */
    public function test_translate_with_locale_override(): void
    {
        LanguageLine::create([
            'group' => 'ui',
            'key' => 'greeting',
            'text' => ['en' => 'Hello', 'ar' => 'مرحبا'],
        ]);

        app('translator')->setLoaded([]);
        app()->setLocale('en');

        $result = translate('Greeting', 'ar');
        $this->assertEquals('مرحبا', $result);

        // Locale should be restored back to 'en'
        $this->assertEquals('en', app()->getLocale());
    }

    /**
     * Test that database migration created language_lines table correctly.
     */
    public function test_language_lines_table_schema(): void
    {
        $this->assertTrue(
            \Illuminate\Support\Facades\Schema::hasTable('language_lines'),
            'language_lines table should exist'
        );

        $this->assertTrue(
            \Illuminate\Support\Facades\Schema::hasColumns('language_lines', ['group', 'key', 'text']),
            'language_lines should have group, key, and text columns'
        );
    }
}
