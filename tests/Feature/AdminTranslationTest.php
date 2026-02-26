<?php

namespace Tests\Feature;

use App\Filament\Enums\NavigationGroups;
use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\CouponResource;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\RoleResource;
use App\Filament\Resources\StaffResource;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

/**
 * Verifies that the Filament admin panel translation system works correctly.
 *
 * Tests cover:
 *  - English labels load and return real strings (not raw keys)
 *  - Arabic labels load and return real Arabic strings (not raw keys)
 *  - Navigation group labels return translated values
 *  - No resource falls back to returning a raw translation key as its label
 */
class AdminTranslationTest extends TestCase
{
    use DatabaseTransactions;
    // ──────────────────── English ────────────────────

    /** @test */
    public function product_resource_labels_return_english_strings(): void
    {
        App::setLocale('en');

        $this->assertEquals('Product', ProductResource::getModelLabel());
        $this->assertEquals('Products', ProductResource::getPluralModelLabel());
        $this->assertEquals('Products', ProductResource::getNavigationLabel());
    }

    /** @test */
    public function category_resource_labels_return_english_strings(): void
    {
        App::setLocale('en');

        $this->assertEquals('Category', CategoryResource::getModelLabel());
        $this->assertEquals('Categories', CategoryResource::getPluralModelLabel());
        $this->assertEquals('Categories', CategoryResource::getNavigationLabel());
    }

    /** @test */
    public function coupon_resource_labels_return_english_strings(): void
    {
        App::setLocale('en');

        $this->assertEquals('Coupon', CouponResource::getModelLabel());
        $this->assertEquals('Coupons', CouponResource::getPluralModelLabel());
    }

    /** @test */
    public function role_resource_labels_return_english_strings(): void
    {
        App::setLocale('en');

        $this->assertEquals('Role', RoleResource::getModelLabel());
        $this->assertEquals('Roles', RoleResource::getPluralModelLabel());
    }

    /** @test */
    public function staff_resource_labels_return_english_strings(): void
    {
        App::setLocale('en');

        $this->assertEquals('Staff / Admin', StaffResource::getModelLabel());
        $this->assertEquals('Staff & Admins', StaffResource::getPluralModelLabel());
    }

    // ──────────────────── Arabic ────────────────────

    /** @test */
    public function product_resource_labels_return_arabic_strings(): void
    {
        App::setLocale('ar');

        $this->assertEquals('منتج', ProductResource::getModelLabel());
        $this->assertEquals('المنتجات', ProductResource::getPluralModelLabel());
    }

    /** @test */
    public function category_resource_labels_return_arabic_strings(): void
    {
        App::setLocale('ar');

        $this->assertEquals('فئة', CategoryResource::getModelLabel());
        $this->assertEquals('الفئات', CategoryResource::getPluralModelLabel());
    }

    /** @test */
    public function coupon_resource_labels_return_arabic_strings(): void
    {
        App::setLocale('ar');

        $this->assertEquals('كوبون', CouponResource::getModelLabel());
        $this->assertEquals('كوبونات الخصم', CouponResource::getPluralModelLabel());
    }

    /** @test */
    public function role_resource_labels_return_arabic_strings(): void
    {
        App::setLocale('ar');

        $this->assertEquals('دور', RoleResource::getModelLabel());
        $this->assertEquals('الأدوار', RoleResource::getPluralModelLabel());
    }

    /** @test */
    public function staff_resource_labels_return_arabic_strings(): void
    {
        App::setLocale('ar');

        $this->assertEquals('موظف / مشرف', StaffResource::getModelLabel());
        $this->assertEquals('الموظفون والمشرفون', StaffResource::getPluralModelLabel());
    }

    // ──────────────────── Navigation Groups ────────────────────

    /** @test */
    public function navigation_group_catalog_returns_english_label(): void
    {
        App::setLocale('en');
        $this->assertEquals('Catalog', NavigationGroups::CATALOG->getLocalizedLabel());
    }

    /** @test */
    public function navigation_group_catalog_returns_arabic_label(): void
    {
        App::setLocale('ar');
        $this->assertEquals('الكتالوج', NavigationGroups::CATALOG->getLocalizedLabel());
    }

    /** @test */
    public function navigation_group_user_management_returns_arabic_label(): void
    {
        App::setLocale('ar');
        $this->assertEquals('إدارة المستخدمين', NavigationGroups::USER_MANAGEMENT->getLocalizedLabel());
    }

    // ──────────────────── No Raw Key Fallbacks ────────────────────

    /** @test */
    public function no_resource_returns_a_raw_translation_key_in_english(): void
    {
        App::setLocale('en');

        $resources = [
            ProductResource::class,
            CategoryResource::class,
            CouponResource::class,
            RoleResource::class,
            StaffResource::class,
        ];

        foreach ($resources as $resource) {
            $label = $resource::getModelLabel();

            $this->assertStringNotContainsString(
                'admin/resources.',
                $label,
                "Resource [{$resource}] returned raw key: [{$label}]"
            );
        }
    }

    /** @test */
    public function all_admin_php_lang_files_exist_and_contain_expected_keys(): void
    {
        $locales = collect(config('app.supported_locales',
            []))->pluck('language')->unique()->values()->toArray() ?: ['en'];
        $files = ['navigation', 'resources', 'actions', 'validation'];

        foreach ($locales as $locale) {
            foreach ($files as $file) {
                $path = lang_path("{$locale}/admin/{$file}.php");

                $this->assertFileExists($path, "Missing lang file: {$locale}/admin/{$file}.php");

                $translations = require $path;
                $this->assertIsArray($translations, "Lang file {$locale}/admin/{$file}.php must return an array");
                $this->assertNotEmpty($translations, "Lang file {$locale}/admin/{$file}.php must not be empty");
            }
        }
    }

    /** @test */
    public function admin_navigation_php_file_contains_all_expected_keys(): void
    {
        $expected = [
            'catalog',
            'shop_management',
            'user_management',
            'settings',
            'products',
            'categories',
            'coupons',
            'staff',
            'roles',
        ];

        $locales = collect(config('app.supported_locales',
            []))->pluck('language')->unique()->values()->toArray() ?: ['en'];
        foreach ($locales as $locale) {
            $translations = require lang_path("{$locale}/admin/navigation.php");

            foreach ($expected as $key) {
                $this->assertArrayHasKey($key, $translations, "Missing key [{$key}] in {$locale}/admin/navigation.php");
            }
        }
    }
}
