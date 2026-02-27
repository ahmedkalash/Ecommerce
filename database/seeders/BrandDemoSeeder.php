<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class BrandDemoSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Brand::truncate();
        Schema::enableForeignKeyConstraints();

        $demo_brands_num = 10;
        while ($demo_brands_num--) {
            Brand::create([
                'name' => ['en' => fake()->sentence(5), 'ar' => fake('ar_EG')->realText(50)],
                'logo' => 'uploads/brands/brand.jpg',
                'top' => rand(0, 1),
                'slug' => fake()->unique()->slug(3),
                'meta_title' => ['en' => fake()->sentence(5), 'ar' => fake('ar_EG')->realText(50)],
                'meta_description' => ['en' => fake()->sentence(5), 'ar' => fake('ar_EG')->realText(50)],
            ]);
        }

    }
}
