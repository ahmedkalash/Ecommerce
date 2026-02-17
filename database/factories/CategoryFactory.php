<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'parent_id' => null,
            'name' => Str::limit($name, 50, ''),
            'commision_rate' => 0.00,
            'discount' => 0.00,
            'discount_start_date' => null,
            'discount_end_date' => null,
            'banner' => null,
            'icon' => null,
            'cover_image' => null,
            'featured' => 0,
            'top' => 0,
            'digital' => 0,
            'slug' => Str::slug($name),
            'refund_request_time' => null,
            'meta_title' => $name,
            'meta_description' => fake()->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the category is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'featured' => 1,
        ]);
    }

    /**
     * Indicate that the category is top.
     */
    public function top(): static
    {
        return $this->state(fn (array $attributes) => [
            'top' => 1,
        ]);
    }

    /**
     * Indicate that the category is digital.
     */
    public function digital(): static
    {
        return $this->state(fn (array $attributes) => [
            'digital' => 1,
        ]);
    }

    /**
     * Indicate that the category is a subcategory.
     */
    public function subCategory(?Category $parent = null): static
    {
        return $this->state(function (array $attributes) use ($parent) {
            $parent = $parent ?? Category::factory()->create();

            return [
                'parent_id' => $parent->id,
            ];
        });
    }
}
