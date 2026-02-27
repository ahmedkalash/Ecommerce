<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL because Doctrine DBAL does not support modifying columns to ENUM natively via change()
        $prefix = DB::getTablePrefix();
        DB::statement("ALTER TABLE {$prefix}product_stocks MODIFY COLUMN video_provider ENUM('youtube', 'dailymotion', 'vimeo') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to VARCHAR(255)
        $prefix = DB::getTablePrefix();
        DB::statement("ALTER TABLE {$prefix}product_stocks MODIFY COLUMN video_provider VARCHAR(255) NULL");
    }
};
