<?php

namespace Tests\Feature;

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
                    'min_qty' => 5, // New field
                    'cash_on_delivery' => 0, // New field check
                    // 'video_link' => '...',
                ],
                [
                    'variant' => 'Blue',
                    'price' => 100,
                    'qty' => 20,
                    'sku' => 'TEST-BLUE',
                    'min_qty' => 1,
                ],
            ],
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
}
