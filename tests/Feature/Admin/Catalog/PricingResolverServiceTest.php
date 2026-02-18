<?php

namespace Tests\Feature\Admin\Catalog;

use App\DataTransferObjects\PriceResult;
use App\Enums\SpecialPriceType;
use App\Models\FlashDeal;
use App\Models\FlashDealProduct;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\WholesalePrice;
use App\Services\PricingResolverService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PricingResolverServiceTest extends TestCase
{
    use DatabaseTransactions;

    private PricingResolverService $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new PricingResolverService;
    }

    // ──────────────────────────────────────────────
    // PriceResult DTO
    // ──────────────────────────────────────────────

    public function test_resolve_returns_price_result_dto(): void
    {
        $stock = ProductStock::factory()->create(['price' => 100]);

        $result = $this->resolver->resolve($stock);

        $this->assertInstanceOf(PriceResult::class, $result);
    }

    // ──────────────────────────────────────────────
    // No special price
    // ──────────────────────────────────────────────

    public function test_no_special_price_returns_base_price(): void
    {
        $stock = ProductStock::factory()->create(['price' => 100.00]);

        $result = $this->resolver->resolve($stock);

        $this->assertSame(100.0, $result->basePrice);
        $this->assertSame(100.0, $result->finalPrice);
        $this->assertSame(0.0, $result->discountAmount);
        $this->assertFalse($result->isSpecialActive);
        $this->assertFalse($result->isFlashDealActive);
        $this->assertFalse($result->isWholesaleApplied);
        $this->assertNull($result->specialPriceType);
    }

    // ──────────────────────────────────────────────
    // Discount percent — active
    // ──────────────────────────────────────────────

    public function test_discount_percent_active(): void
    {
        $stock = ProductStock::factory()
            ->withSpecialPrice(value: 10, type: SpecialPriceType::DiscountPercent)
            ->create(['price' => 100.00]);

        $result = $this->resolver->resolve($stock);

        $this->assertTrue($result->isSpecialActive);
        $this->assertSame(100.0, $result->basePrice);
        $this->assertSame(90.0, $result->finalPrice);
        $this->assertSame(10.0, $result->discountAmount);
        $this->assertSame(SpecialPriceType::DiscountPercent, $result->specialPriceType);
    }

    // ──────────────────────────────────────────────
    // Fixed price — active
    // ──────────────────────────────────────────────

    public function test_fixed_price_active(): void
    {
        $stock = ProductStock::factory()
            ->withSpecialPrice(value: 75, type: SpecialPriceType::FixedPrice)
            ->create(['price' => 100.00]);

        $result = $this->resolver->resolve($stock);

        $this->assertTrue($result->isSpecialActive);
        $this->assertSame(75.0, $result->finalPrice);
        $this->assertSame(25.0, $result->discountAmount);
        $this->assertSame(SpecialPriceType::FixedPrice, $result->specialPriceType);
    }

    // ──────────────────────────────────────────────
    // Expired special price
    // ──────────────────────────────────────────────

    public function test_expired_special_price_returns_base_price(): void
    {
        $stock = ProductStock::factory()
            ->withExpiredSpecialPrice(value: 20)
            ->create(['price' => 100.00]);

        $result = $this->resolver->resolve($stock);

        $this->assertFalse($result->isSpecialActive);
        $this->assertSame(100.0, $result->finalPrice);
        $this->assertSame(0.0, $result->discountAmount);
    }

    // ──────────────────────────────────────────────
    // Future special price
    // ──────────────────────────────────────────────

    public function test_future_special_price_returns_base_price(): void
    {
        $stock = ProductStock::factory()
            ->withFutureSpecialPrice(value: 20)
            ->create(['price' => 100.00]);

        $result = $this->resolver->resolve($stock);

        $this->assertFalse($result->isSpecialActive);
        $this->assertSame(100.0, $result->finalPrice);
    }

    // ──────────────────────────────────────────────
    // No date range → NOT active (both dates required)
    // ──────────────────────────────────────────────

    public function test_no_date_range_special_price_not_active(): void
    {
        $stock = ProductStock::factory()->create([
            'price' => 200.00,
            'special_price' => 50,
            'special_price_type' => SpecialPriceType::DiscountPercent,
            'special_price_start' => null,
            'special_price_end' => null,
        ]);

        $result = $this->resolver->resolve($stock);

        $this->assertFalse($result->isSpecialActive);
        $this->assertSame(200.0, $result->finalPrice);
    }

    // ──────────────────────────────────────────────
    // 100% discount → final price = 0
    // ──────────────────────────────────────────────

    public function test_100_percent_discount_results_in_zero(): void
    {
        $stock = ProductStock::factory()
            ->withSpecialPrice(value: 100, type: SpecialPriceType::DiscountPercent)
            ->create(['price' => 50.00]);

        $result = $this->resolver->resolve($stock);

        $this->assertTrue($result->isSpecialActive);
        $this->assertSame(0.0, $result->finalPrice);
        $this->assertSame(50.0, $result->discountAmount);
    }

    // ──────────────────────────────────────────────
    // Fix price zero → clamped to 0
    // ──────────────────────────────────────────────

    public function test_fixed_price_zero_clamps_to_zero(): void
    {
        $stock = ProductStock::factory()
            ->withSpecialPrice(value: 0, type: SpecialPriceType::FixedPrice)
            ->create(['price' => 100.00]);

        $result = $this->resolver->resolve($stock);

        $this->assertTrue($result->isSpecialActive);
        $this->assertSame(0.0, $result->finalPrice);
    }

    // ──────────────────────────────────────────────
    // Edge: only one date set → NOT active
    // ──────────────────────────────────────────────

    public function test_only_start_date_set_not_active(): void
    {
        $stock = ProductStock::factory()->create([
            'price' => 100.00,
            'special_price' => 20,
            'special_price_type' => SpecialPriceType::DiscountPercent,
            'special_price_start' => now()->subDay(),
            'special_price_end' => null,
        ]);

        $result = $this->resolver->resolve($stock);

        $this->assertFalse($result->isSpecialActive);
        $this->assertSame(100.0, $result->finalPrice);
    }

    public function test_only_end_date_set_not_active(): void
    {
        $stock = ProductStock::factory()->create([
            'price' => 100.00,
            'special_price' => 30,
            'special_price_type' => SpecialPriceType::DiscountPercent,
            'special_price_start' => null,
            'special_price_end' => now()->addDay(),
        ]);

        $result = $this->resolver->resolve($stock);

        $this->assertFalse($result->isSpecialActive);
        $this->assertSame(100.0, $result->finalPrice);
    }

    // ──────────────────────────────────────────────
    // isSpecialPriceActive on service directly
    // ──────────────────────────────────────────────

    public function test_service_is_special_price_active(): void
    {
        $active = ProductStock::factory()
            ->withSpecialPrice()
            ->create(['price' => 100]);

        $expired = ProductStock::factory()
            ->withExpiredSpecialPrice()
            ->create(['price' => 100]);

        $noDates = ProductStock::factory()->create([
            'price' => 100,
            'special_price' => 10,
            'special_price_type' => SpecialPriceType::DiscountPercent,
            'special_price_start' => null,
            'special_price_end' => null,
        ]);

        $this->assertTrue($this->resolver->isSpecialPriceActive($active));
        $this->assertFalse($this->resolver->isSpecialPriceActive($expired));
        $this->assertFalse($this->resolver->isSpecialPriceActive($noDates));
    }

    // ══════════════════════════════════════════════
    // WHOLESALE PRICING
    // ══════════════════════════════════════════════

    public function test_wholesale_price_applied_when_quantity_in_range(): void
    {
        $product = Product::factory()->create(['wholesale_product' => true]);
        $variant = ProductStock::factory()->create([
            'product_id' => $product->id,
            'price' => 100.00,
        ]);

        WholesalePrice::create([
            'product_stock_id' => $variant->id,
            'min_qty' => 5,
            'max_qty' => 20,
            'price' => 80.00,
        ]);

        $result = $this->resolver->resolve($variant, quantity: 10);

        $this->assertSame(100.0, $result->basePrice);
        $this->assertSame(80.0, $result->finalPrice);
        $this->assertTrue($result->isWholesaleApplied);
    }

    public function test_wholesale_price_not_applied_below_min_qty(): void
    {
        $product = Product::factory()->create(['wholesale_product' => true]);
        $variant = ProductStock::factory()->create([
            'product_id' => $product->id,
            'price' => 100.00,
        ]);

        WholesalePrice::create([
            'product_stock_id' => $variant->id,
            'min_qty' => 10,
            'max_qty' => 50,
            'price' => 80.00,
        ]);

        $result = $this->resolver->resolve($variant, quantity: 3);

        $this->assertSame(100.0, $result->finalPrice);
        $this->assertFalse($result->isWholesaleApplied);
    }

    public function test_wholesale_price_not_applied_above_max_qty(): void
    {
        $product = Product::factory()->create(['wholesale_product' => true]);
        $variant = ProductStock::factory()->create([
            'product_id' => $product->id,
            'price' => 100.00,
        ]);

        WholesalePrice::create([
            'product_stock_id' => $variant->id,
            'min_qty' => 5,
            'max_qty' => 20,
            'price' => 80.00,
        ]);

        $result = $this->resolver->resolve($variant, quantity: 25);

        $this->assertSame(100.0, $result->finalPrice);
        $this->assertFalse($result->isWholesaleApplied);
    }

    public function test_wholesale_not_applied_when_product_not_wholesale(): void
    {
        $product = Product::factory()->create(['wholesale_product' => false]);
        $variant = ProductStock::factory()->create([
            'product_id' => $product->id,
            'price' => 100.00,
        ]);

        WholesalePrice::create([
            'product_stock_id' => $variant->id,
            'min_qty' => 1,
            'max_qty' => 100,
            'price' => 80.00,
        ]);

        $result = $this->resolver->resolve($variant, quantity: 10);

        $this->assertSame(100.0, $result->finalPrice);
        $this->assertFalse($result->isWholesaleApplied);
    }

    public function test_wholesale_at_exact_min_qty_boundary(): void
    {
        $product = Product::factory()->create(['wholesale_product' => true]);
        $variant = ProductStock::factory()->create([
            'product_id' => $product->id,
            'price' => 100.00,
        ]);

        WholesalePrice::create([
            'product_stock_id' => $variant->id,
            'min_qty' => 5,
            'max_qty' => 20,
            'price' => 85.00,
        ]);

        $result = $this->resolver->resolve($variant, quantity: 5);

        $this->assertSame(85.0, $result->finalPrice);
        $this->assertTrue($result->isWholesaleApplied);
    }

    public function test_wholesale_at_exact_max_qty_boundary(): void
    {
        $product = Product::factory()->create(['wholesale_product' => true]);
        $variant = ProductStock::factory()->create([
            'product_id' => $product->id,
            'price' => 100.00,
        ]);

        WholesalePrice::create([
            'product_stock_id' => $variant->id,
            'min_qty' => 5,
            'max_qty' => 20,
            'price' => 85.00,
        ]);

        $result = $this->resolver->resolve($variant, quantity: 20);

        $this->assertSame(85.0, $result->finalPrice);
        $this->assertTrue($result->isWholesaleApplied);
    }

    // ══════════════════════════════════════════════
    // FLASH DEAL PRICING
    // ══════════════════════════════════════════════

    public function test_flash_deal_amount_discount_applied(): void
    {
        $product = Product::factory()->create();
        $variant = ProductStock::factory()->create([
            'product_id' => $product->id,
            'price' => 100.00,
        ]);

        $flashDeal = FlashDeal::forceCreate([
            'title' => 'Test Flash Amount',
            'status' => 1,
            'featured' => 0,
            'start_date' => now()->subHour()->timestamp,
            'end_date' => now()->addDay()->timestamp,
        ]);

        FlashDealProduct::forceCreate([
            'flash_deal_id' => $flashDeal->id,
            'product_id' => $product->id,
            'discount' => 15.00,
            'discount_type' => 'amount',
        ]);

        $result = $this->resolver->resolve($variant);

        $this->assertSame(100.0, $result->basePrice);
        $this->assertSame(85.0, $result->finalPrice);
        $this->assertSame(15.0, $result->discountAmount);
        $this->assertTrue($result->isFlashDealActive);
        $this->assertFalse($result->isSpecialActive);
    }

    public function test_flash_deal_percent_discount_applied(): void
    {
        $product = Product::factory()->create();
        $variant = ProductStock::factory()->create([
            'product_id' => $product->id,
            'price' => 200.00,
        ]);

        $flashDeal = FlashDeal::forceCreate([
            'title' => 'Test Flash Percent',
            'status' => 1,
            'featured' => 0,
            'start_date' => now()->subHour()->timestamp,
            'end_date' => now()->addDay()->timestamp,
        ]);

        FlashDealProduct::forceCreate([
            'flash_deal_id' => $flashDeal->id,
            'product_id' => $product->id,
            'discount' => 25.00,
            'discount_type' => 'percent',
        ]);

        $result = $this->resolver->resolve($variant);

        $this->assertSame(200.0, $result->basePrice);
        $this->assertSame(150.0, $result->finalPrice);
        $this->assertTrue($result->isFlashDealActive);
    }

    public function test_expired_flash_deal_ignored(): void
    {
        $product = Product::factory()->create();
        $variant = ProductStock::factory()->create([
            'product_id' => $product->id,
            'price' => 100.00,
        ]);

        $flashDeal = FlashDeal::forceCreate([
            'title' => 'Expired Flash',
            'status' => 1,
            'featured' => 0,
            'start_date' => now()->subWeek()->timestamp,
            'end_date' => now()->subDay()->timestamp,
        ]);

        FlashDealProduct::forceCreate([
            'flash_deal_id' => $flashDeal->id,
            'product_id' => $product->id,
            'discount' => 15.00,
            'discount_type' => 'amount',
        ]);

        $result = $this->resolver->resolve($variant);

        $this->assertSame(100.0, $result->finalPrice);
        $this->assertFalse($result->isFlashDealActive);
    }

    public function test_inactive_flash_deal_ignored(): void
    {
        $product = Product::factory()->create();
        $variant = ProductStock::factory()->create([
            'product_id' => $product->id,
            'price' => 100.00,
        ]);

        $flashDeal = FlashDeal::forceCreate([
            'title' => 'Inactive Flash',
            'status' => 0, // Inactive
            'featured' => 0,
            'start_date' => now()->subHour()->timestamp,
            'end_date' => now()->addDay()->timestamp,
        ]);

        FlashDealProduct::forceCreate([
            'flash_deal_id' => $flashDeal->id,
            'product_id' => $product->id,
            'discount' => 15.00,
            'discount_type' => 'amount',
        ]);

        $result = $this->resolver->resolve($variant);

        $this->assertSame(100.0, $result->finalPrice);
        $this->assertFalse($result->isFlashDealActive);
    }

    // ══════════════════════════════════════════════
    // PRIORITY / PRECEDENCE
    // ══════════════════════════════════════════════

    public function test_flash_deal_takes_priority_over_special_price(): void
    {
        $product = Product::factory()->create();
        $variant = ProductStock::factory()
            ->withSpecialPrice(
                value: 10.0,
                type: SpecialPriceType::DiscountPercent,
                start: now()->subDay(),
                end: now()->addWeek(),
            )
            ->create([
                'product_id' => $product->id,
                'price' => 100.00,
            ]);

        $flashDeal = FlashDeal::forceCreate([
            'title' => 'Priority Flash',
            'status' => 1,
            'featured' => 0,
            'start_date' => now()->subHour()->timestamp,
            'end_date' => now()->addDay()->timestamp,
        ]);

        FlashDealProduct::forceCreate([
            'flash_deal_id' => $flashDeal->id,
            'product_id' => $product->id,
            'discount' => 30.00,
            'discount_type' => 'amount',
        ]);

        $result = $this->resolver->resolve($variant);

        // Flash deal should apply: 100 - 30 = 70
        $this->assertSame(70.0, $result->finalPrice);
        $this->assertTrue($result->isFlashDealActive);
        $this->assertFalse($result->isSpecialActive);
    }

    public function test_wholesale_and_special_price_combined(): void
    {
        // Wholesale replaces base price, then special price applies on top
        $product = Product::factory()->create(['wholesale_product' => true]);
        $variant = ProductStock::factory()
            ->withSpecialPrice(
                value: 10.0,
                type: SpecialPriceType::DiscountPercent,
                start: now()->subDay(),
                end: now()->addWeek(),
            )
            ->create([
                'product_id' => $product->id,
                'price' => 100.00,
            ]);

        WholesalePrice::create([
            'product_stock_id' => $variant->id,
            'min_qty' => 5,
            'max_qty' => 50,
            'price' => 80.00,
        ]);

        $result = $this->resolver->resolve($variant, quantity: 10);

        // Wholesale sets price to 80, then 10% discount: 80 - 8 = 72
        $this->assertTrue($result->isWholesaleApplied);
        $this->assertTrue($result->isSpecialActive);
        $this->assertSame(72.0, $result->finalPrice);
    }

    // ══════════════════════════════════════════════
    // EDGE CASES
    // ══════════════════════════════════════════════

    public function test_zero_base_price_variant(): void
    {
        $variant = ProductStock::factory()->create(['price' => 0.00]);

        $result = $this->resolver->resolve($variant);

        $this->assertSame(0.0, $result->basePrice);
        $this->assertSame(0.0, $result->finalPrice);
        $this->assertSame(0.0, $result->discountAmount);
    }

    public function test_default_quantity_is_one(): void
    {
        $product = Product::factory()->create(['wholesale_product' => true]);
        $variant = ProductStock::factory()->create([
            'product_id' => $product->id,
            'price' => 100.00,
        ]);

        WholesalePrice::create([
            'product_stock_id' => $variant->id,
            'min_qty' => 5,
            'max_qty' => 50,
            'price' => 80.00,
        ]);

        // Default quantity = 1, so wholesale should NOT apply
        $result = $this->resolver->resolve($variant);

        $this->assertSame(100.0, $result->finalPrice);
        $this->assertFalse($result->isWholesaleApplied);
    }
}
