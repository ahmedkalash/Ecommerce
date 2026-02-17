<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'name' => $name,
            'added_by' => 'admin',
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            // Should match new pivot structure? If existing schema still has category_id, keep it.
            // 'brand_id' => Brand::factory(), // Optional
            'description' => fake()->paragraph(),
            'published' => 1,
            'approved' => 1,

            'shipping_type' => 'flat_rate',
            'shipping_cost' => 0,
            'est_shipping_days' => 2,

            'meta_title' => $name,
            'meta_description' => $name,
            'slug' => Str::slug($name).'-'.Str::random(5),

            'rating' => 0,
            'barcode' => null,
            'digital' => 0,

            'file_name' => null,
            'file_path' => null,
            'external_link' => null,
            'external_link_btn' => 'Buy Now',
            'wholesale_product' => 0,
            'frequently_bought_selection_type' => 'product',
            'has_warranty' => 0,
            'warranty_id' => null,
            'warranty_note_id' => null,
            'extra_attributes' => [],
        ];
    }
}
