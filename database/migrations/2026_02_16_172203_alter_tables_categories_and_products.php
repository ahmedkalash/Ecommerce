<?php

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
        Schema::table('categories', function (Blueprint $table) {
            $table->string('name', 255)->change();
            $table->string('banner', 255)->nullable()->change();
            $table->string('icon', 255)->nullable()->change();
            $table->string('cover_image', 255)->nullable()->change();
            $table->decimal('commision_rate', 8, 2)->default(0.00)->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('name', 255)->change();
            $table->decimal('shipping_cost', 8, 2)->default(0.00)->change();
            $table->decimal('rating', 8, 2)->default(0.00)->change();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
