<?php

namespace Tests\Feature\Admin\Catalog;

use App\DataTransferObjects\ProductData;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\MediaService;
use App\Services\ProductService;
use App\Services\ProductStockService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    protected ProductService $productService;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock MediaService
        $mediaService = $this->mock(MediaService::class, function ($mock) {
            $mock->shouldReceive('syncMedia')->byDefault();
        });

        // Mock ProductStockService
        $productStockService = $this->mock(ProductStockService::class, function ($mock) {
            $mock->shouldReceive('store')->byDefault();
        });

        // Inject mocks
        $this->productService = new ProductService($mediaService, $productStockService);
    }

    /** @test */
    public function it_creates_product_successfully_ignoring_removed_columns(): void
    {
        $user = User::factory()->create(['user_type' => 'admin']);
        $this->actingAs($user);

        $category = Category::factory()->create();

        $data = ProductData::fromArray([
            'name' => 'Test Product Creation',
            'category_ids' => [$category->id],
            'description' => '<p>Test Description</p>',
            'tags' => ['unit', 'test'],
            'stocks' => [],
        ]);

        $product = $this->productService->store($data);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Test Product Creation',
        ]);
    }

    /** @test */
    public function it_updates_product_successfully_ignoring_removed_columns(): void
    {
        $user = User::factory()->create(['user_type' => 'admin']);
        $this->actingAs($user);

        $product = Product::factory()->create();

        $updateData = ProductData::fromArray([
            'name' => 'Updated Product Name',
            'description' => '<p>Updated Description</p>',
            'stocks' => [],
        ]);

        $updatedProduct = $this->productService->update($updateData, $product);

        $this->assertInstanceOf(Product::class, $updatedProduct);
        $this->assertEquals('Updated Product Name', $updatedProduct->name);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product Name',
        ]);
    }

    /** @test */
    public function it_creates_product_from_legacy_array_with_fallback(): void
    {
        $user = User::factory()->create(['user_type' => 'admin']);
        $this->actingAs($user);

        // Simulating legacy form data (no 'stocks' key, uses 'unit_price' etc.)
        $data = ProductData::fromArray([
            'name' => 'Legacy Product',
            'unit_price' => 99.99,
            'current_stock' => 25,
            'sku' => 'LEGACY-001',
            'min_qty' => 2,
        ]);

        // Verify the DTO correctly builds a Default stock from legacy data
        $this->assertCount(1, $data->stocks);
        $this->assertEquals('Default', $data->stocks[0]->variant);
        $this->assertEquals(99.99, $data->stocks[0]->price);
        $this->assertEquals(25, $data->stocks[0]->qty);
        $this->assertEquals('LEGACY-001', $data->stocks[0]->sku);
        $this->assertEquals(2, $data->stocks[0]->min_qty);

        $product = $this->productService->store($data);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Legacy Product',
        ]);
    }

    /** @test */
    public function it_duplicates_product_correctly(): void
    {
        $user = User::factory()->create(['user_type' => 'admin']);
        $this->actingAs($user);

        // Use real ProductStockService for duplication
        $stockService = $this->mock(ProductStockService::class, function ($mock) {
            $mock->shouldReceive('store')->byDefault();
            $mock->shouldReceive('product_duplicate_store')->once();
        });

        $service = new ProductService(
            app(MediaService::class),
            $stockService
        );

        $product = Product::factory()->create(['name' => 'Original Product']);

        $duplicate = $service->duplicate($product);

        $this->assertNotEquals($product->id, $duplicate->id);
        $this->assertEquals('Original Product', $duplicate->name);
        $this->assertNotEquals($product->slug, $duplicate->slug);
    }
}
