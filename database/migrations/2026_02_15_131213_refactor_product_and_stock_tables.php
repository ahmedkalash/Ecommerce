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
        // 1. Add new columns to product_stocks
        Schema::table('product_stocks', function (Blueprint $table) {
            $table->integer('min_qty')->default(1);
            $table->boolean('cash_on_delivery')->default(1);
        });

        // 2. Migrate data from products to product_stocks
        // We need to copy min_qty and cash_on_delivery from parent product to all its stocks.
        $products = DB::table('products')->select('id', 'min_qty', 'cash_on_delivery')->get();
        foreach ($products as $product) {
            DB::table('product_stocks')
                ->where('product_id', $product->id)
                ->update([
                    'min_qty' => $product->min_qty ?? 1,
                    'cash_on_delivery' => $product->cash_on_delivery ?? 1,
                ]);
        }

        // 3. Drop legacy columns from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'variations',
                'variant_product',
                'attributes',
                'colors',
                'todays_deal',
                'stock_visibility_state',
                'cash_on_delivery',
                'featured',
                'seller_featured',
                'min_qty',
                'low_stock_quantity',
                'discount',
                'discount_type',
                'discount_start_date',
                'discount_end_date',
                'tax',
                'tax_type',
                'weight',
                'is_quantity_multiplied',
                'num_of_sale',
                'auction_product',
            ]);
        });
    }

    public function down(): void
    {
        // Reverse operation
        Schema::table('products', function (Blueprint $table) {
            // Re-add columns (nullable to avoid issues)
            $table->text('variations')->nullable();
            $table->integer('variant_product')->default(0);
            $table->text('attributes')->nullable();
            $table->text('colors')->nullable();
            $table->integer('todays_deal')->default(0);
            $table->string('stock_visibility_state')->default('quantity');
            $table->boolean('cash_on_delivery')->default(1);
            $table->integer('featured')->default(0);
            $table->integer('seller_featured')->default(0);
            $table->integer('min_qty')->default(1);
            $table->integer('low_stock_quantity')->default(0);
            $table->double('discount')->default(0);
            $table->string('discount_type')->nullable();
            $table->integer('discount_start_date')->nullable();
            $table->integer('discount_end_date')->nullable();
            $table->double('tax')->default(0);
            $table->string('tax_type')->nullable();
            $table->double('weight')->default(0);
            $table->boolean('is_quantity_multiplied')->default(0);
            $table->integer('num_of_sale')->default(0);
            $table->integer('auction_product')->default(0);
        });

        // We can optionally migrate data back, but it's complex since stocks > 1.
        // We'll just drop the columns from product_stocks
        Schema::table('product_stocks', function (Blueprint $table) {
            $table->dropColumn(['min_qty', 'cash_on_delivery']);
        });
    }
};
