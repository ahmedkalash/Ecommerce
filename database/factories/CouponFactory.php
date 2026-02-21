<?php

namespace Database\Factories;

use App\Enums\Coupons\CouponDiscountTypes;
use App\Enums\Coupons\CouponTypes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => CouponTypes::CART_BASED,
            'label' => fake()->words(3, true),
            'code' => strtoupper(fake()->unique()->lexify('??????')).fake()->unique()->numerify('####'),
            'discount' => fake()->randomFloat(2, 5, 50),
            'discount_type' => fake()->randomElement(CouponDiscountTypes::cases()),
            'min_money_spent' => fake()->optional(0.7)->randomFloat(2, 50, 500),
            'max_discount' => fake()->optional(0.5)->randomFloat(2, 10, 100),
            'usage_limit' => fake()->optional(0.5)->numberBetween(10, 1000),
            'used_count' => 0,
            'product_ids' => [],
            'start_date' => Carbon::now()->subDays(fake()->numberBetween(0, 5)),
            'end_date' => Carbon::now()->addDays(fake()->numberBetween(1, 30)),
        ];
    }

    /**
     * Indicate that the coupon is cart based.
     */
    public function cartBased(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => CouponTypes::CART_BASED,
            'product_ids' => [],
        ]);
    }

    /**
     * Indicate that the coupon is product based.
     */
    public function productBased(array $productIds = []): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => CouponTypes::PRODUCT_BASED,
            'product_ids' => empty($productIds) ? [fake()->numberBetween(1, 100)] : $productIds,
        ]);
    }
}
