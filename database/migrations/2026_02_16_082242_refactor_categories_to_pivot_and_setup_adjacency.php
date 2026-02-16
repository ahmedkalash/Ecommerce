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
        // 1. Ensure product_categories table structure (pivot)
        // Drop existing table to ensure clean state (since we are refactoring and populating from source)
        Schema::dropIfExists('product_categories');

        Schema::create('product_categories', function (Blueprint $table) {
            // No surrogate id, use composite primary key
            $table->integer('product_id');
            $table->integer('category_id');
            $table->timestamps();

            // Composite Primary Key (Ensure Uniqueness and Indexing)
            $table->primary(['product_id', 'category_id']);

            // Explicit Foreign Keys with Cascade
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();

            // Explicit Index for Reverse Lookup
            $table->index('category_id');
        });

        // 2. Migrate existing category_id from products table to pivot
        // Use raw SQL for performance and to avoid placeholder limits
        // Use INSERT IGNORE to skip duplicates if running multiple times
        // Filter out orphaned records using INNER JOIN
        DB::statement('
            INSERT IGNORE INTO product_categories (product_id, category_id, created_at, updated_at)
            SELECT p.id, p.category_id, NOW(), NOW()
            FROM products p
            INNER JOIN categories c ON c.id = p.category_id
            WHERE p.category_id IS NOT NULL
        ');

        // 3. Drop category_id from products table
        // 3. Drop category_id from products table
        if (Schema::hasColumn('products', 'category_id')) {
            try {
                Schema::table('products', function (Blueprint $table) {
                    $table->dropForeign(['category_id']);
                });
            } catch (\Exception $e) {
                // Ignore if FK doesn't exist
            }

            try {
                Schema::table('products', function (Blueprint $table) {
                    $table->dropIndex(['category_id']);
                });
            } catch (\Exception $e) {
                // Ignore if Index doesn't exist
            }

            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('category_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Add category_id back to products
        Schema::table('products', function (Blueprint $table) {
            // Need to match exactly what it was (int, nullable, FK to categories)
            $table->integer('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
        });

        // 2. Restore primary category from pivot (pick the first one found)
        $pivots = DB::table('product_categories')
            ->select('product_id', 'category_id')
            ->orderBy('category_id') // Use category_id for stable ordering since id removed
            ->get()
            ->groupBy('product_id');

        foreach ($pivots as $productId => $categories) {
            // We can't batch update easily with varying values, so loop is okay for down migration
            $mainCatId = $categories->first()->category_id;
            DB::table('products')->where('id', $productId)->update(['category_id' => $mainCatId]);
        }
    }
};
