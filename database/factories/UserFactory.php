<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * User Factory - Laravel 10 Class-Based Style
 *
 * Provides flexible test data generation with states for different user types and verification statuses.
 *
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // Test password: "password"
            'remember_token' => Str::random(10),
            'user_type' => 'customer', // Default
            'banned' => 0,
            'is_suspicious' => 0,
            'phone' => '+'.fake()->unique()->numerify('1##########'), // US format
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'country' => fake()->country(),
        ];
    }

    /**
     * Indicate that the user is a customer.
     */
    public function customer(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'customer',
        ]);
    }

    /**
     * Indicate that the user is a seller.
     */
    public function seller(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'seller',
        ]);
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'admin',
        ]);
    }

    /**
     * Indicate that the user is a staff member.
     */
    public function staff(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'staff',
        ]);
    }

    /**
     * Indicate that the user is a delivery boy.
     */
    public function deliveryBoy(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'delivery_boy',
        ]);
    }

    /**
     * Indicate that the user's email is unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
            'verification_code' => (string) rand(100000, 999999),
        ]);
    }

    /**
     * Indicate that the user is banned.
     */
    public function banned(): static
    {
        return $this->state(fn (array $attributes) => [
            'banned' => 1,
        ]);
    }

    /**
     * Indicate that the user is marked as suspicious.
     */
    public function suspicious(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_suspicious' => 1,
        ]);
    }

    /**
     * Indicate that the user authenticated via social provider.
     */
    public function socialAuth(string $provider = 'google', ?string $providerId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'provider' => $provider,
            'provider_id' => $providerId ?? fake()->uuid(),
            'email_verified_at' => now(), // Social auth users are auto-verified
        ]);
    }

    /**
     * Indicate that the user registered with phone only (no email).
     */
    public function phoneOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => null,
            'phone' => '+'.fake()->unique()->numerify('1##########'),
        ]);
    }
}
