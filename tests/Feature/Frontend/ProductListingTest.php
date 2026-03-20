<?php

namespace Tests\Feature\Frontend;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProductListingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['scout.driver' => 'collection']);

        DB::table('business_settings')->updateOrInsert(['type' => 'decimal_separator'], ['value' => '1']);
        DB::table('business_settings')->updateOrInsert(['type' => 'symbol_format'], ['value' => '1']);
        DB::table('business_settings')->updateOrInsert(['type' => 'no_of_decimals'], ['value' => '2']);

        DB::table('currencies')->updateOrInsert(
            ['id' => 1],
            ['name' => 'US Dollar', 'code' => 'USD', 'symbol' => '$', 'exchange_rate' => 1, 'status' => 1]
        );
        DB::table('business_settings')->updateOrInsert(['type' => 'system_default_currency'], ['value' => '1']);
    }

    public function test_it_can_render_the_product_listing_page()
    {
        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertSeeLivewire('frontend.catalog.catalog-search');
        $response->assertSeeLivewire('frontend.header-search');
    }

    public function test_it_can_render_the_category_listing_page()
    {
        $category = Category::factory()->create();

        $response = $this->get(route('products.category', $category->slug));

        $response->assertStatus(200);
        $response->assertSeeLivewire('frontend.catalog.catalog-search');
    }

    public function test_it_can_render_the_brand_listing_page()
    {
        $brand = Brand::factory()->create();

        $response = $this->get(route('products.brand', $brand->slug));

        $response->assertStatus(200);
        $response->assertSeeLivewire('frontend.catalog.catalog-search');
    }
}
