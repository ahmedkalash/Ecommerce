<?php

namespace Database\Factories;

use App\Models\Brand;
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
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'video_provider' => 'youtube',
            'video_link' => null,
            'tags' => null,
            'description' => fake()->paragraph(),
            'unit_price' => fake()->randomFloat(2, 10, 1000),
            'purchase_price' => fake()->randomFloat(2, 5, 500),
            'variant_product' => 0,
            'attributes' => '[]',
            'choice_options' => '[]',
            'colors' => '[]',
            'variations' => '[]',
            'todays_deal' => 0,
            'published' => 1,
            'approved' => 1,
            'stock_visibility_state' => 'quantity',
            'cash_on_delivery' => 1,
            'featured' => 0,
            'seller_featured' => 0,
            'current_stock' => 100,
            'unit' => 'pc',
            'weight' => 0,
            'min_qty' => 1,
            'low_stock_quantity' => 1,
            'discount' => 0,
            'discount_type' => 'amount',
            'tax' => 0,
            'tax_type' => 'amount',
            'shipping_type' => 'flat_rate',
            'shipping_cost' => 0,
            'is_quantity_multiplied' => 0,
            'est_shipping_days' => 2,
            'num_of_sale' => 0,
            'meta_title' => $name,
            'meta_description' => $name,
            'meta_img' => null,
            'pdf' => null,
            'slug' => Str::slug($name).'-'.Str::random(5),
            'rating' => 0,
            'barcode' => null,
            'digital' => 0,
            'auction_product' => 0,
            'file_name' => null,
            'file_path' => null,
        ];
    }
}
