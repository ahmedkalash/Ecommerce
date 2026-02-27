<?php

namespace Database\Factories;

use App\Enums\SpecialPriceType;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductStockFactory extends Factory
{
    protected $model = ProductStock::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'variant' => $this->faker->unique()->word(),
            'sku' => $this->faker->unique()->bothify('SKU-####'),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'qty' => $this->faker->numberBetween(0, 100),
            'min_qty' => 1,
            'special_price' => null,
            'special_price_type' => null,
            'special_price_start' => null,
            'special_price_end' => null,
        ];
    }

    public function withSpecialPrice(
        float $value = 10,
        SpecialPriceType $type = SpecialPriceType::DiscountPercent,
        $start = null,
        $end = null
    ): self {
        return $this->state(fn (array $attributes) => [
            'special_price' => $value,
            'special_price_type' => $type,
            'special_price_start' => $start ?? now()->subDay(),
            'special_price_end' => $end ?? now()->addDay(),
        ]);
    }

    public function withExpiredSpecialPrice(float $value = 10): self
    {
        return $this->state(fn (array $attributes) => [
            'special_price' => $value,
            'special_price_type' => SpecialPriceType::DiscountPercent,
            'special_price_start' => now()->subWeek(),
            'special_price_end' => now()->subDay(),
        ]);
    }

    public function withFutureSpecialPrice(float $value = 10): self
    {
        return $this->state(fn (array $attributes) => [
            'special_price' => $value,
            'special_price_type' => SpecialPriceType::DiscountPercent,
            'special_price_start' => now()->addDay(),
            'special_price_end' => now()->addWeek(),
        ]);
    }
}
