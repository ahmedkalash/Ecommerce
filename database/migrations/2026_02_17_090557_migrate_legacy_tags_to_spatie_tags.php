<?php

use App\Models\Product;
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
        // 1. Migrate existing tags to Spatie Tags
        if (Schema::hasColumn('products', 'tags')) {
            $products = DB::table('products')->whereNotNull('tags')->where('tags', '!=', '')->select('id',
                'tags')->get();

            foreach ($products as $product) {
                $tagNames = array_filter(array_map('trim', explode(',', $product->tags)));
                if (! empty($tagNames)) {
                    // We use the model to leverage Spatie's logic
                    $productModel = Product::find($product->id);
                    if ($productModel) {
                        $productModel->attachTags($tagNames);
                    }
                }
            }

            // 2. Drop the legacy column
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('tags');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('tags')->nullable();
        });
    }
};
