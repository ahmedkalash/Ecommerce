<?php

namespace Tests\Feature\Admin\Catalog;

use App\DTOs\ProductDTO;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
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

        // Mock ProductStockService
        $productStockService = $this->mock(ProductStockService::class, function ($mock) {
            $mock->shouldReceive('store')->byDefault();
        });

        // Inject mocks
        $this->productService = new ProductService($productStockService);
    }

    /** @test */
    public function it_creates_product_successfully_ignoring_removed_columns(): void
    {
        $user = User::factory()->create(['user_type' => 'admin']);
        $this->actingAs($user);

        $category = Category::factory()->create();

        $data = ProductDTO::fromArray([
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

        $updateData = ProductDTO::fromArray([
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
            $stockService
        );

        $product = Product::factory()->create(['name' => 'Original Product']);

        $duplicate = $service->duplicate($product);

        $this->assertNotEquals($product->id, $duplicate->id);
        $this->assertEquals('Original Product', $duplicate->name);
        $this->assertNotEquals($product->slug, $duplicate->slug);
    }
}
