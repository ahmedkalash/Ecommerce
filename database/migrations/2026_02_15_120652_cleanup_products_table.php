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
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'unit_price',
                'purchase_price',
                'current_stock',
                'video_provider',
                'video_link',
                // 'photos', 'thumbnail_img' etc were removed in previous migrations
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->double('unit_price', 20, 2)->default(0.00);
            $table->double('purchase_price', 20, 2)->nullable();
            $table->integer('current_stock')->default(0);
            $table->string('video_provider')->nullable();
            $table->longText('video_link')->nullable();
        });
    }
};
