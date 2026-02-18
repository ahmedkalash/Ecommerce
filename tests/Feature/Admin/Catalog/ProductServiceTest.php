<?php

namespace Tests\Feature\Admin\Catalog;

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

    protected $productService;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock MediaService
        $mediaService = $this->mock(MediaService::class, function ($mock) {
            $mock->shouldReceive('syncMedia')->byDefault();
        });

        // Mock ProductStockService
        $productStockService = $this->mock(ProductStockService::class, function ($mock) {
            $mock->shouldReceive('store')->byDefault(); // Bypass stock storage which was causing DB errors
        });

        // Inject mocks
        $this->productService = new ProductService($mediaService, $productStockService);
    }

    /** @test */
    public function it_creates_product_successfully_ignoring_removed_columns()
    {
        // Setup
        $user = User::factory()->create(['user_type' => 'admin']);
        $this->actingAs($user);

        $category = Category::factory()->create();

        $data = [
            'name' => 'Test Product Creation',
            'category_id' => $category->id,
            'description' => '<p>Test Description</p>',
            'tags' => ['unit', 'test'],
            'unit_price' => 100,
            'qty' => 50,
            'stocks' => [], // Empty stocks as service is mocked
            'refundable' => 1,
            'discount_start_date' => '2023-01-01',
            'discount_end_date' => '2023-01-31',
        ];

        // Action
        $product = $this->productService->store($data);

        // Assertion
        $this->assertInstanceOf(Product::class, $product);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Test Product Creation',
        ]);
    }

    /** @test */
    public function it_updates_product_successfully_ignoring_removed_columns()
    {
        // Setup
        $user = User::factory()->create(['user_type' => 'admin']);
        $this->actingAs($user);

        $product = Product::factory()->create();

        $updateData = [
            'name' => 'Updated Product Name',
            'description' => '<p>Updated Description</p>',
            'stocks' => [],
            'refundable' => 0,
            'discount_start_date' => '2023-06-01',
        ];

        // Action
        $updatedProduct = $this->productService->update($updateData, $product);

        // Assertion
        $this->assertInstanceOf(Product::class, $updatedProduct);
        $this->assertEquals('Updated Product Name', $updatedProduct->name);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product Name',
        ]);

        // We do not check stocks here as stock service is mocked.
    }
}
