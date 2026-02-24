<?php

namespace Tests\Feature\Admin\Catalog;

use App\DTOs\ProductStockDTO;
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
            'stocks' => [
                [
                    'variant' => 'Red',
                    'price' => 100,
                    'qty' => 50,
                    'sku' => 'TEST-RED',
                    'min_qty' => 5,
                    'cash_on_delivery' => 0,
                ],
                [
                    'variant' => 'Blue',
                    'price' => 100,
                    'qty' => 20,
                    'sku' => 'TEST-BLUE',
                    'min_qty' => 1,
                    'cash_on_delivery' => 1,
                ],
            ],
        ];

        // Action
        $stocks = collect($data['stocks'])->map(fn ($s) => ProductStockDTO::fromArray($s))->toArray();
        $this->productStockService->store($product, ...$stocks);

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

        $data = [
            'stocks' => [
                ['variant' => 'Red-Small-Cotton', 'price' => 50, 'qty' => 10, 'sku' => 'R-S-C'],
                ['variant' => 'Red-Small-Silk', 'price' => 55],
                ['variant' => 'Red-Large-Cotton', 'price' => 60],
                ['variant' => 'Red-Large-Silk', 'price' => 65],
                ['variant' => 'Blue-Small-Cotton', 'price' => 70],
                ['variant' => 'Blue-Small-Silk', 'price' => 75],
                ['variant' => 'Blue-Large-Cotton', 'price' => 80],
                ['variant' => 'Blue-Large-Silk', 'price' => 85, 'qty' => 5, 'sku' => 'B-L-S'],
            ],
        ];

        $stocks = collect($data['stocks'])->map(fn ($s) => ProductStockDTO::fromArray($s))->toArray();
        $this->productStockService->store($product, ...$stocks);

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
            'price' => 85,
            'sku' => 'B-L-S',
        ]);
    }

    /** @test */
    public function it_stores_product_stocks_with_special_price()
    {
        $user = User::factory()->create(['user_type' => 'admin']);
        $this->actingAs($user);

        $product = Product::factory()->create();

        $startDate = now()->addDays(1)->startOfSecond();
        $endDate = now()->addDays(7)->startOfSecond();

        $data = [
            'stocks' => [
                [
                    'variant' => 'Red',
                    'price' => 100,
                    'qty' => 50,
                    'sku' => 'SPEC-RED',
                    'special_price_type' => 'discount_percent',
                    'special_price' => 20,
                    'special_price_start' => $startDate->toDateTimeString(),
                    'special_price_end' => $endDate->toDateTimeString(),
                ],
            ],
        ];

        $stocks = collect($data['stocks'])->map(fn ($s) => ProductStockDTO::fromArray($s))->toArray();
        $this->productStockService->store($product, ...$stocks);

        $this->assertDatabaseHas('product_stocks', [
            'product_id' => $product->id,
            'variant' => 'Red',
            'price' => 100,
            'sku' => 'SPEC-RED',
            'special_price' => 20,
            'special_price_type' => 'discount_percent',
            'special_price_start' => $startDate->toDateTimeString(),
            'special_price_end' => $endDate->toDateTimeString(),
        ]);
    }
}
