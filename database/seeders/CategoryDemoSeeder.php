<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CategoryDemoSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        ProductCategory::truncate();
        Category::truncate();
        Schema::enableForeignKeyConstraints();

        $demo_categories_num = 10;
        while ($demo_categories_num--) {
            Category::create([
                'name' => ['en' => fake()->sentence(5), 'ar' => fake('ar_EG')->realText(50)],
                'slug' => fake()->unique()->slug(3),
                'banner' => 'uploads/categories/banner/category-banner.jpg',
                'icon' => 'uploads/categories/icon/KjJP9wuEZNL184XVUk3S7EiZ8NnBN99kiU4wdvp3.png',
                'meta_title' => ['en' => fake()->sentence(5), 'ar' => fake('ar_EG')->realText(50)],
                'meta_description' => ['en' => fake()->sentence(5), 'ar' => fake('ar_EG')->realText(50)],
                'cover_image' => null,
                'featured' => rand(0, 1),
                'top' => rand(0, 1),
                'digital' => rand(0, 1),
            ]);
        }

    }
}
