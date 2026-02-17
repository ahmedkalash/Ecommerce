<?php

use App\Models\ProductStock;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Migrate variant data to extra_attributes
        $colors = config('attributes.presets.color.options', []);

        // Iterate through stocks that have a variant set
        ProductStock::whereNotNull('variant')->where('variant', '!=', '')->chunk(100, function ($stocks) use ($colors) {
            foreach ($stocks as $stock) {
                // Check if variant matches a known color
                if (isset($colors[$stock->variant])) {
                    $stock->extra_attributes->set('color', $stock->variant);
                    // We can also store the color code for convenience, or rely on config lookup
                    // Storing it makes it self-contained in the JSON
                    $stock->extra_attributes->set('color_code', $colors[$stock->variant]);
                } else {
                    // Unknown variant (or combination like 'Red-XL'), store as generic for now
                    // Based on analysis, most are simple color names
                    $stock->extra_attributes->set('variant_name', $stock->variant);
                }

                $stock->save();
            }
        });

        // 2. Drop legacy attribute tables
        Schema::dropIfExists('attribute_category');
        Schema::dropIfExists('attribute_translations');
        Schema::dropIfExists('attribute_values');

        // Disable foreign key checks tailored for attributes/colors if any exist
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('colors');
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We cannot easily restore data dropped from tables, but we can recreate the schema.

        Schema::create('colors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->timestamps();
        });

        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->integer('attribute_id');
            $table->string('value');
            $table->string('color_code')->nullable();
            $table->timestamps();
        });

        Schema::create('attribute_translations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('attribute_id');
            $table->string('name');
            $table->string('lang');
            $table->timestamps();
        });

        Schema::create('attribute_category', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id');
            $table->integer('attribute_id');
            $table->timestamps();
        });
    }
};
