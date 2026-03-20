<?php

namespace Tests\Feature\Frontend;

use App\Livewire\Frontend\Catalog\CatalogSearch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class CatalogSearchTest extends TestCase
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

    public function test_it_renders_product_cards(): void
    {
        $product = Product::factory()->create(['name' => 'Test Gaming Mouse', 'published' => 1, 'approved' => 1]);
        $product->stocks()->create(['variant' => '', 'price' => 50, 'qty' => 10]);

        Livewire::test(CatalogSearch::class)
            ->assertSee($product->name);
    }

    public function test_it_filters_by_search_keyword(): void
    {
        $product1 = Product::factory()->create(['name' => 'Apple iPhone', 'published' => 1, 'approved' => 1]);
        $product1->stocks()->create(['variant' => '', 'price' => 50, 'qty' => 10]);
        $product2 = Product::factory()->create(['name' => 'Samsung Galaxy', 'published' => 1, 'approved' => 1]);
        $product2->stocks()->create(['variant' => '', 'price' => 50, 'qty' => 10]);

        Livewire::test(CatalogSearch::class)
            ->set('search', 'Apple')
            ->assertSee('Apple iPhone')
            ->assertDontSee('Samsung Galaxy');
    }

    public function test_it_listens_to_search_updated_event(): void
    {
        $product1 = Product::factory()->create(['name' => 'Apple iPhone', 'published' => 1, 'approved' => 1]);
        $product1->stocks()->create(['variant' => '', 'price' => 50, 'qty' => 10]);
        $product2 = Product::factory()->create(['name' => 'Samsung Galaxy', 'published' => 1, 'approved' => 1]);
        $product2->stocks()->create(['variant' => '', 'price' => 50, 'qty' => 10]);

        Livewire::test(CatalogSearch::class)
            ->dispatch('searchUpdated', 'Apple')
            ->assertSet('search', 'Apple')
            ->assertSee('Apple iPhone')
            ->assertDontSee('Samsung Galaxy');
    }

    // ── Category tree filter tests ────────────────────────────────────────────

    public function test_selected_categories_defaults_to_empty_array(): void
    {
        Livewire::test(CatalogSearch::class)
            ->assertSet('selectedCategories', []);
    }

    public function test_it_filters_products_by_single_selected_category(): void
    {
        $catA = Category::factory()->create([
            'name' => json_encode(['en' => 'Electronics']), 'slug' => 'electronics-test',
        ]);
        $catB = Category::factory()->create(['name' => json_encode(['en' => 'Clothing']), 'slug' => 'clothing-test']);

        $prodA = Product::factory()->create(['name' => 'Laptop Test', 'published' => 1, 'approved' => 1]);
        $prodA->stocks()->create(['variant' => 'default-a', 'price' => 999, 'qty' => 5]);
        $prodA->categories()->syncWithoutDetaching([$catA->id]);

        $prodB = Product::factory()->create(['name' => 'T-Shirt Test', 'published' => 1, 'approved' => 1]);
        $prodB->stocks()->create(['variant' => 'default-b', 'price' => 20, 'qty' => 50]);
        $prodB->categories()->syncWithoutDetaching([$catB->id]);

        Livewire::test(CatalogSearch::class)
            ->set('selectedCategories', ['electronics-test'])
            ->assertSee('Laptop Test')
            ->assertDontSee('T-Shirt Test');
    }

    public function test_it_filters_products_by_multiple_selected_categories(): void
    {
        $catA = Category::factory()->create([
            'name' => json_encode(['en' => 'Electronics']), 'slug' => 'electronics-multi',
        ]);
        $catB = Category::factory()->create(['name' => json_encode(['en' => 'Clothing']), 'slug' => 'clothing-multi']);
        $catC = Category::factory()->create(['name' => json_encode(['en' => 'Books']), 'slug' => 'books-multi']);

        $prodA = Product::factory()->create(['name' => 'Laptop Multi', 'published' => 1, 'approved' => 1]);
        $prodA->stocks()->create(['variant' => 'ela', 'price' => 999, 'qty' => 5]);
        $prodA->categories()->syncWithoutDetaching([$catA->id]);

        $prodB = Product::factory()->create(['name' => 'T-Shirt Multi', 'published' => 1, 'approved' => 1]);
        $prodB->stocks()->create(['variant' => 'clb', 'price' => 20, 'qty' => 50]);
        $prodB->categories()->syncWithoutDetaching([$catB->id]);

        $prodC = Product::factory()->create(['name' => 'Novel Multi', 'published' => 1, 'approved' => 1]);
        $prodC->stocks()->create(['variant' => 'bkc', 'price' => 15, 'qty' => 30]);
        $prodC->categories()->syncWithoutDetaching([$catC->id]);

        Livewire::test(CatalogSearch::class)
            ->set('selectedCategories', ['electronics-multi', 'clothing-multi'])
            ->assertSee('Laptop Multi')
            ->assertSee('T-Shirt Multi')
            ->assertDontSee('Novel Multi');
    }

    // ── Brand filter tests ────────────────────────────────────────────────────

    public function test_selected_brands_defaults_to_empty_array(): void
    {
        Livewire::test(CatalogSearch::class)
            ->assertSet('selectedBrands', []);
    }

    public function test_it_filters_products_by_single_selected_brand(): void
    {
        $brandA = Brand::factory()->create(['name' => json_encode(['en' => 'Nike']), 'slug' => 'nike-brand']);
        $brandB = Brand::factory()->create(['name' => json_encode(['en' => 'Adidas']), 'slug' => 'adidas-brand']);

        $prodA = Product::factory()->create([
            'name' => 'Nike Shoes', 'published' => 1, 'approved' => 1, 'brand_id' => $brandA->id,
        ]);
        $prodA->stocks()->create(['variant' => 'nk1', 'price' => 100, 'qty' => 5]);

        $prodB = Product::factory()->create([
            'name' => 'Adidas Shoes', 'published' => 1, 'approved' => 1, 'brand_id' => $brandB->id,
        ]);
        $prodB->stocks()->create(['variant' => 'ad1', 'price' => 90, 'qty' => 5]);

        Livewire::test(CatalogSearch::class)
            ->set('selectedBrands', ['nike-brand'])
            ->assertSee('Nike Shoes')
            ->assertDontSee('Adidas Shoes');
    }

    public function test_it_filters_products_by_multiple_selected_brands(): void
    {
        $brandA = Brand::factory()->create(['name' => json_encode(['en' => 'Nike']), 'slug' => 'nike-multi']);
        $brandB = Brand::factory()->create(['name' => json_encode(['en' => 'Adidas']), 'slug' => 'adidas-multi']);
        $brandC = Brand::factory()->create(['name' => json_encode(['en' => 'Puma']), 'slug' => 'puma-multi']);

        $prodA = Product::factory()->create([
            'name' => 'Nike Multi', 'published' => 1, 'approved' => 1, 'brand_id' => $brandA->id,
        ]);
        $prodA->stocks()->create(['variant' => 'nkm', 'price' => 100, 'qty' => 3]);

        $prodB = Product::factory()->create([
            'name' => 'Adidas Multi', 'published' => 1, 'approved' => 1, 'brand_id' => $brandB->id,
        ]);
        $prodB->stocks()->create(['variant' => 'adm', 'price' => 90, 'qty' => 3]);

        $prodC = Product::factory()->create([
            'name' => 'Puma Multi', 'published' => 1, 'approved' => 1, 'brand_id' => $brandC->id,
        ]);
        $prodC->stocks()->create(['variant' => 'pum', 'price' => 80, 'qty' => 3]);

        Livewire::test(CatalogSearch::class)
            ->set('selectedBrands', ['nike-multi', 'adidas-multi'])
            ->assertSee('Nike Multi')
            ->assertSee('Adidas Multi')
            ->assertDontSee('Puma Multi');
    }

    // ── Clear filters ─────────────────────────────────────────────────────────

    public function test_clearing_filters_resets_selected_categories_and_brands(): void
    {
        Livewire::test(CatalogSearch::class)
            ->set('selectedCategories', ['electronics', 'clothing'])
            ->set('selectedBrands', ['nike', 'adidas'])
            ->set('selectedCategories', [])
            ->set('selectedBrands', [])
            ->assertSet('selectedCategories', [])
            ->assertSet('selectedBrands', []);
    }
}
