<?php

namespace Tests\Feature\Admin\Catalog;

use App\DTOs\PriceResultDTO;
use App\Enums\SpecialPriceType;
use App\Models\ProductStock;
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

    // ─── Basic Resolution ────────────────────────────────────────────────

    public function test_resolve_returns_price_result_dto(): void
    {
        $stock = ProductStock::factory()->create(['price' => 100]);

        $result = $this->resolver->resolve($stock);

        $this->assertInstanceOf(PriceResultDTO::class, $result);
    }

    public function test_no_special_price_returns_base_price(): void
    {
        $stock = ProductStock::factory()->create(['price' => 100.00]);

        $result = $this->resolver->resolve($stock);

        $this->assertSame(100.0, $result->basePrice);
        $this->assertSame(100.0, $result->finalPrice);
        $this->assertSame(0.0, $result->discountAmount);
        $this->assertFalse($result->isSpecialActive);
        $this->assertNull($result->specialPriceType);
    }

    // ─── Discount Percent ────────────────────────────────────────────────

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

    public function test_100_percent_discount_clamps_to_zero(): void
    {
        $stock = ProductStock::factory()
            ->withSpecialPrice(value: 100, type: SpecialPriceType::DiscountPercent)
            ->create(['price' => 50.00]);

        $result = $this->resolver->resolve($stock);

        $this->assertSame(0.0, $result->finalPrice);
        $this->assertSame(50.0, $result->discountAmount);
    }

    // ─── Fixed Price ─────────────────────────────────────────────────────

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

    public function test_fixed_price_higher_than_base_returns_fixed_price(): void
    {
        // Edge case: fixed_price > base, no clamping here — it just returns the fixed price.
        $stock = ProductStock::factory()
            ->withSpecialPrice(value: 200, type: SpecialPriceType::FixedPrice)
            ->create(['price' => 100.00]);

        $result = $this->resolver->resolve($stock);

        $this->assertTrue($result->isSpecialActive);
        $this->assertSame(200.0, $result->finalPrice);
    }

    // ─── Date-Range Behavior ─────────────────────────────────────────────

    public function test_expired_special_price_is_not_applied(): void
    {
        $stock = ProductStock::factory()
            ->withExpiredSpecialPrice(value: 50)
            ->create(['price' => 100.00]);

        $result = $this->resolver->resolve($stock);

        $this->assertFalse($result->isSpecialActive);
        $this->assertSame(100.0, $result->finalPrice);
    }

    public function test_future_special_price_is_not_applied(): void
    {
        $stock = ProductStock::factory()
            ->withFutureSpecialPrice(value: 50)
            ->create(['price' => 100.00]);

        $result = $this->resolver->resolve($stock);

        $this->assertFalse($result->isSpecialActive);
        $this->assertSame(100.0, $result->finalPrice);
    }

    public function test_null_date_range_means_special_is_inactive(): void
    {
        $stock = ProductStock::factory()->create([
            'price' => 100.00,
            'special_price' => 20.00,
            'special_price_type' => SpecialPriceType::DiscountPercent,
            'special_price_start' => null,
            'special_price_end' => null,
        ]);

        $result = $this->resolver->resolve($stock);

        $this->assertFalse($result->isSpecialActive);
        $this->assertSame(100.0, $result->finalPrice);
    }

    // ─── Edge Cases ──────────────────────────────────────────────────────

    public function test_zero_base_price_with_special(): void
    {
        $stock = ProductStock::factory()
            ->withSpecialPrice(value: 50, type: SpecialPriceType::DiscountPercent)
            ->create(['price' => 0.00]);

        $result = $this->resolver->resolve($stock);

        $this->assertSame(0.0, $result->finalPrice);
    }
}
