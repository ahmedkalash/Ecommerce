<?php

namespace Database\Factories;

use App\Enums\SpecialPriceType;
use App\Models\Product;
use App\Models\ProductStock;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductStock>
 */
class ProductStockFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductStock::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'variant' => $this->faker->unique()->word,
            'sku' => $this->faker->unique()->ean8(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'qty' => $this->faker->numberBetween(0, 100),
        ];
    }

    /**
     * State: variant with an active special price.
     */
    public function withSpecialPrice(
        float $value = 10.00,
        SpecialPriceType $type = SpecialPriceType::DiscountPercent,
        ?Carbon $start = null,
        ?Carbon $end = null,
    ): static {
        return $this->state([
            'special_price' => $value,
            'special_price_type' => $type,
            'special_price_start' => $start ?? now()->subDay(),
            'special_price_end' => $end ?? now()->addWeek(),
        ]);
    }

    /**
     * State: variant with an expired special price.
     */
    public function withExpiredSpecialPrice(
        float $value = 10.00,
        SpecialPriceType $type = SpecialPriceType::DiscountPercent,
    ): static {
        return $this->state([
            'special_price' => $value,
            'special_price_type' => $type,
            'special_price_start' => now()->subMonth(),
            'special_price_end' => now()->subWeek(),
        ]);
    }

    /**
     * State: variant with a future special price.
     */
    public function withFutureSpecialPrice(
        float $value = 10.00,
        SpecialPriceType $type = SpecialPriceType::DiscountPercent,
    ): static {
        return $this->state([
            'special_price' => $value,
            'special_price_type' => $type,
            'special_price_start' => now()->addWeek(),
            'special_price_end' => now()->addMonth(),
        ]);
    }
}
