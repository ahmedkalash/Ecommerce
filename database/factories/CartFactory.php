<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Cart>
 */
class CartFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Cart::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => 1,
            'owner_id' => User::factory(),
            'user_id' => User::factory(),
            'temp_user_id' => null,
            'address_id' => 0,
            'product_id' => Product::factory(),
            'variation' => null,
            'price' => fake()->randomFloat(2, 10, 500),
            'tax' => fake()->randomFloat(2, 0, 50),
            'shipping_cost' => fake()->randomFloat(2, 0, 20),
            'shipping_type' => 'flat_rate',
            'pickup_point' => null,
            'carrier_id' => null,
            'discount' => 0.00,
            'product_referral_code' => null,
            'coupon_code' => null,
            'coupon_applied' => 0,
            'quantity' => fake()->numberBetween(1, 5),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * State for a guest cart (temp_user_id instead of user_id)
     */
    public function guest(): static
    {
        return $this->state(fn(array $attributes) => [
            'user_id' => null,
            'temp_user_id' => Str::random(32),
        ]);
    }
}
