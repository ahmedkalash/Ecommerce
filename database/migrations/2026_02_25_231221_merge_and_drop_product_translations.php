<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Merges remaining product translations directly into the `products`
     * table JSON columns, and then drops the `product_translations`
     * table to finalize the translatable conversion.
     */
    public function up(): void
    {
        if (Schema::hasTable('product_translations')) {
            // Get all translations to merge
            $translations = DB::table('product_translations')->get();

            foreach ($translations as $tr) {
                $product = DB::table('products')->where('id', $tr->product_id)->first();
                if (! $product) {
                    continue;
                }

                $names = json_decode($product->name, true) ?? [];
                $descriptions = json_decode($product->description, true) ?? [];

                if (! empty($tr->name)) {
                    $names[$tr->lang] = $tr->name;
                }
                if (! empty($tr->description)) {
                    $descriptions[$tr->lang] = $tr->description;
                }

                DB::table('products')->where('id', $tr->product_id)->update([
                    'name' => json_encode($names, JSON_UNESCAPED_UNICODE),
                    'description' => json_encode($descriptions, JSON_UNESCAPED_UNICODE),
                ]);
            }

            Schema::dropIfExists('product_translations');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-create the product_translations table
        Schema::create('product_translations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('lang', 10);
            $table->timestamps();
        });
    }
};
