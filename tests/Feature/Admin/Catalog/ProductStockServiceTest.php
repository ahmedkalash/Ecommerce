<?php

namespace Tests\Feature\Admin\Catalog;

use App\Models\Product;
use App\Models\User;
use App\Services\ProductStockService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductStockServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected ProductStockService $productStockService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productStockService = app(ProductStockService::class);

        // Fix testing DB schema if missing columns
        if (Schema::hasTable('product_stocks')) {
            Schema::table('product_stocks', function (Blueprint $table) {
                if (! Schema::hasColumn('product_stocks', 'min_qty')) {
                    $table->integer('min_qty')->default(1);
                }
                if (! Schema::hasColumn('product_stocks', 'video_link')) {
                    $table->string('video_link')->nullable();
                }
                if (! Schema::hasColumn('product_stocks', 'video_provider')) {
                    $table->string('video_provider')->nullable();
                }
                if (! Schema::hasColumn('product_stocks', 'cash_on_delivery')) {
                    $table->tinyInteger('cash_on_delivery')->default(1);
                }
                if (! Schema::hasColumn('product_stocks', 'extra_attributes')) {
                    $table->json('extra_attributes')->nullable();
                }
            });
        }
    }

    /** @test */
    public function it_stores_product_stocks_correctly_with_min_qty()
    {
        // Setup
        $user = User::factory()->create(['user_type' => 'admin']);
        $this->actingAs($user);

        $product = Product::factory()->create();

        $data = [
            'colors_active' => '1',
            'colors' => ['Red', 'Blue'],
            // Red Variant Data
            'price_Red' => 100,
            'qty_Red' => 50,
            'sku_Red' => 'TEST-RED',
            'min_qty_Red' => 5,
            'cash_on_delivery_Red' => 0,
            // Blue Variant Data
            'price_Blue' => 100,
            'qty_Blue' => 20,
            'sku_Blue' => 'TEST-BLUE',
            'min_qty_Blue' => 1,
            // 'cash_on_delivery_Blue' defaults to 1
        ];

        // Action
        $this->productStockService->store($data, $product);

        // Assertion
        $this->assertDatabaseHas('product_stocks', [
            'product_id' => $product->id,
            'variant' => 'Red',
            'min_qty' => 5,
            'cash_on_delivery' => 0,
        ]);

        $this->assertDatabaseHas('product_stocks', [
            'product_id' => $product->id,
            'variant' => 'Blue',
            'min_qty' => 1,
        ]);

        $this->assertCount(2, $product->stocks);
    }

    /** @test */
    public function it_handles_complex_variant_combinations()
    {
        $user = User::factory()->create(['user_type' => 'admin']);
        $this->actingAs($user);

        $product = Product::factory()->create();

        // Simulate 3 attributes: Color, Size, Type
        $data = [
            'colors_active' => '1',
            'colors' => ['Red', 'Blue'], // Attribute 1 (Colors)
            'choice_no' => [1, 2], // Attribute IDs from choice_options
            'choice_options_1' => ['Small', 'Large'], // Attribute 2 (Size)
            'choice_options_2' => ['Cotton', 'Silk'], // Attribute 3 (Type)

            // Expected Combinations:
            // Red-Small-Cotton
            // Red-Small-Silk
            // Red-Large-Cotton
            // Red-Large-Silk
            // Blue-Small-Cotton
            // ... (Total 2 * 2 * 2 = 8 variants)

            // For brevity, we verify a few specific keys that the service expects
            'price_Red-Small-Cotton' => 50,
            'qty_Red-Small-Cotton' => 10,
            'sku_Red-Small-Cotton' => 'R-S-C',
            'extra_attributes_Red-Small-Cotton' => json_encode(['specs' => 'test']),

            'price_Blue-Large-Silk' => 80,
            'qty_Blue-Large-Silk' => 5,
            'sku_Blue-Large-Silk' => 'B-L-S',
        ];

        $this->productStockService->store($data, $product);

        // Assert 8 variants created
        $this->assertCount(8, $product->stocks);

        // Assert specific variant data
        $this->assertDatabaseHas('product_stocks', [
            'product_id' => $product->id,
            'variant' => 'Red-Small-Cotton',
            'price' => 50,
            'sku' => 'R-S-C',
        ]);

        $this->assertDatabaseHas('product_stocks', [
            'product_id' => $product->id,
            'variant' => 'Blue-Large-Silk',
            'price' => 80,
            'sku' => 'B-L-S',
        ]);
    }
}
