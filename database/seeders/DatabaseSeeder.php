<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // You should preserve the order of these seeders, not to get errors.

        // app data
        $this->call([
            RoleAndPermissionSeeder::class,
            AssignSuperAdminSeeder::class,
        ]);

        // demo data
        $this->call([
            CategoryDemoSeeder::class,
            BrandDemoSeeder::class,
            ProductDemoSeeder::class,
            CouponDemoSeeder::class,
        ]);
    }
}
