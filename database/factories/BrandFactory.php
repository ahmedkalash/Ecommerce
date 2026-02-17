<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Brand>
 */
class BrandFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Brand::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'name' => Str::limit($name, 50, ''),
            'logo' => null,
            'top' => 0,
            'slug' => Str::slug($name),
            'meta_title' => $name,
            'meta_description' => fake()->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the brand is top.
     */
    public function top(): static
    {
        return $this->state(fn (array $attributes) => [
            'top' => 1,
        ]);
    }
}
