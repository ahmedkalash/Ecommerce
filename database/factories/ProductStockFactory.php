<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductStock>
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
            'variant' => $this->faker->unique()->word, // Or use a generated variant name
            'sku' => $this->faker->unique()->ean8(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'qty' => $this->faker->numberBetween(0, 100),
            'image' => null, // Deprecated but might be in base schema before migration, safe to null or omit if dropped
            // 'video_provider' => null, // Added in recent migration
            // 'video_link' => null, // Added in recent migration
        ];
    }
}
