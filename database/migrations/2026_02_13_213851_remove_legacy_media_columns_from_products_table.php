<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Remove legacy media columns from the `products` table.
 *
 * These columns stored media IDs/paths directly on the products table.
 * They have been replaced by the Spatie Media Library, which uses the
 * polymorphic `media` table with named collections:
 *   - photos         → 'gallery' collection
 *   - thumbnail_img  → 'thumbnail' collection
 *   - meta_img       → 'meta' collection
 *   - pdf            → 'pdf' collection
 *   - short_video    → 'short_video' collection
 *   - short_video_thumbnail → 'video_thumbnail' collection
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'photos',
                'thumbnail_img',
                'meta_img',
                'pdf',
                'short_video',
                'short_video_thumbnail',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('photos', 2000)->nullable()->after('brand_id');
            $table->string('thumbnail_img')->nullable()->after('photos');
            $table->string('short_video')->nullable()->after('thumbnail_img');
            $table->string('short_video_thumbnail')->nullable()->after('short_video');
            $table->string('meta_img')->nullable()->after('meta_description');
            $table->string('pdf')->nullable()->after('meta_img');
        });
    }
};
