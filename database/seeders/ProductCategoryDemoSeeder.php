<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ProductCategoryDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        ProductCategory::truncate();
        Schema::enableForeignKeyConstraints();

        $products_categories = [
            ['product_id' => 2, 'category_id' => 1], ['product_id' => 2, 'category_id' => 8],
            ['product_id' => 2, 'category_id' => 9], ['product_id' => 2, 'category_id' => 48],
            ['product_id' => 3, 'category_id' => 2], ['product_id' => 3, 'category_id' => 6],
            ['product_id' => 3, 'category_id' => 7], ['product_id' => 3, 'category_id' => 47],
            ['product_id' => 4, 'category_id' => 4], ['product_id' => 4, 'category_id' => 10],
            ['product_id' => 4, 'category_id' => 11], ['product_id' => 4, 'category_id' => 25],
            ['product_id' => 5, 'category_id' => 4], ['product_id' => 5, 'category_id' => 10],
            ['product_id' => 5, 'category_id' => 11], ['product_id' => 5, 'category_id' => 25],
            ['product_id' => 6, 'category_id' => 5], ['product_id' => 6, 'category_id' => 12],
            ['product_id' => 6, 'category_id' => 13],
            ['product_id' => 7, 'category_id' => 17], ['product_id' => 7, 'category_id' => 28],
            ['product_id' => 7, 'category_id' => 29], ['product_id' => 7, 'category_id' => 30],
            ['product_id' => 7, 'category_id' => 31],
            ['product_id' => 8, 'category_id' => 17], ['product_id' => 8, 'category_id' => 28],
            ['product_id' => 8, 'category_id' => 29], ['product_id' => 8, 'category_id' => 30],
            ['product_id' => 8, 'category_id' => 31],
            ['product_id' => 9, 'category_id' => 17], ['product_id' => 9, 'category_id' => 28],
            ['product_id' => 9, 'category_id' => 29], ['product_id' => 9, 'category_id' => 30],
            ['product_id' => 9, 'category_id' => 31],
            ['product_id' => 10, 'category_id' => 1], ['product_id' => 10, 'category_id' => 8],
            ['product_id' => 10, 'category_id' => 9], ['product_id' => 10, 'category_id' => 48],
            ['product_id' => 11, 'category_id' => 2], ['product_id' => 11, 'category_id' => 6],
            ['product_id' => 11, 'category_id' => 7], ['product_id' => 11, 'category_id' => 47],
            ['product_id' => 12, 'category_id' => 1], ['product_id' => 12, 'category_id' => 8],
            ['product_id' => 12, 'category_id' => 9], ['product_id' => 12, 'category_id' => 48],
            ['product_id' => 13, 'category_id' => 2], ['product_id' => 13, 'category_id' => 6],
            ['product_id' => 13, 'category_id' => 7], ['product_id' => 13, 'category_id' => 47],
            ['product_id' => 14, 'category_id' => 2], ['product_id' => 14, 'category_id' => 6],
            ['product_id' => 14, 'category_id' => 7], ['product_id' => 14, 'category_id' => 47],
            ['product_id' => 15, 'category_id' => 2], ['product_id' => 15, 'category_id' => 6],
            ['product_id' => 15, 'category_id' => 7], ['product_id' => 15, 'category_id' => 47],
            ['product_id' => 16, 'category_id' => 1], ['product_id' => 16, 'category_id' => 8],
            ['product_id' => 16, 'category_id' => 9], ['product_id' => 16, 'category_id' => 48],
            ['product_id' => 17, 'category_id' => 4], ['product_id' => 17, 'category_id' => 10],
            ['product_id' => 17, 'category_id' => 11], ['product_id' => 17, 'category_id' => 25],
            ['product_id' => 18, 'category_id' => 4], ['product_id' => 18, 'category_id' => 10],
            ['product_id' => 18, 'category_id' => 11], ['product_id' => 18, 'category_id' => 25],
            ['product_id' => 19, 'category_id' => 4], ['product_id' => 19, 'category_id' => 10],
            ['product_id' => 19, 'category_id' => 11], ['product_id' => 19, 'category_id' => 25],
            ['product_id' => 20, 'category_id' => 1], ['product_id' => 20, 'category_id' => 8],
            ['product_id' => 20, 'category_id' => 9], ['product_id' => 20, 'category_id' => 48],
            ['product_id' => 21, 'category_id' => 17], ['product_id' => 21, 'category_id' => 28],
            ['product_id' => 21, 'category_id' => 29], ['product_id' => 21, 'category_id' => 30],
            ['product_id' => 21, 'category_id' => 31],
            ['product_id' => 22, 'category_id' => 17], ['product_id' => 22, 'category_id' => 28],
            ['product_id' => 22, 'category_id' => 29], ['product_id' => 22, 'category_id' => 30],
            ['product_id' => 22, 'category_id' => 31],
        ];

        ProductCategory::insert($products_categories);

    }
}
