<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Optimize products table
        Schema::table('products', function (Blueprint $table) {
            // Change slug to varchar(191) first so it can be indexed
            $table->string('slug', 255)->change();

            // Add unique index on slug
            $table->unique('slug');

            // Add indexes and FK constraints
            // We use 'index' first because some DB engines require it before adding FK
            $table->index('category_id');
            $table->index('name');
            $table->index('brand_id');
            $table->index('user_id');

            // Fix user_id column type to match users.id (int(9) unsigned)
            // This is critical because users table is created with INT(9) UNSIGNED in base schema
            DB::statement('ALTER TABLE products MODIFY user_id INT(9) UNSIGNED NOT NULL');

            // Add Foreign Keys
            $table->foreign('category_id')->references('id')->on('categories')->restrictOnDelete();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 2. Optimize product_stocks table
        Schema::table('product_stocks', function (Blueprint $table) {
            // Add index for product_id
            $table->index('product_id');

            // Add Foreign Key for product_id
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();

            // Add Unique Constraint for SKU (Unique across the whole store)
            $table->unique('sku');

            // Add Composite Unique Index for (product_id + variant)
            // This ensures a product cannot have duplicate variants "Red-Small"
            $table->unique(['product_id', 'variant']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_stocks', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropIndex(['product_id']);
            $table->dropUnique(['sku']);
            $table->dropUnique(['product_id', 'variant']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['brand_id']);
            $table->dropForeign(['user_id']);

            $table->dropIndex(['category_id']);
            $table->dropIndex(['brand_id']);
            $table->dropIndex(['user_id']);

            $table->dropUnique(['slug']);
            $table->mediumText('slug')->change();
        });
    }
};
