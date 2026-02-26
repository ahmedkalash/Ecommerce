<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ProductDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::get();
        $brands = Brand::get();

        Schema::disableForeignKeyConstraints();
        ProductStock::truncate();
        Product::truncate();
        Schema::enableForeignKeyConstraints();

        $demo_products_num = 10;
        while ($demo_products_num--) {
            $product = Product::create([
                'name' => ['en' => fake()->sentence(5), 'ar' => fake('ar_EG')->realText(50)],
                'description' => ['en' => fake()->sentence(10), 'ar' => fake('ar_EG')->realText(50)],
                'slug' => fake()->unique(1)->slug(3), 'brand_id' => $brands->random()->id,
                'user_id' => 1, 'published' => 1, 'approved' => 1, 'digital' => rand(0, 1), 'rating' => rand(0, 5),
                'has_warranty' => rand(0, 1),
            ]);

            // Create product stocks
            $demo_product_stocks_num = 3;
            while ($demo_product_stocks_num--) {
                $stocks = [
                    'product_id' => $product->id, 'variant' => fake()->unique()->slug(),
                    'sku' => fake()->unique()->slug(),
                    'price' => 5.32, 'qty' => rand(0, 1000), 'video_provider' => null, 'video_link' => null,
                    'min_qty' => rand(1, 2), 'cash_on_delivery' => rand(0, 1),
                ];
                ProductStock::create($stocks);
            }

            // Sync category
            $product->categories()->syncWithoutDetaching([$categories->random()->id, $categories->random()->id]);
        }

    }
}
