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
            // Only drop columns if they exist (for existing installations)
            // Fresh installations from updated base_schema.sql won't have these columns
            if (Schema::hasColumn('categories', 'level')) {
                $table->dropColumn('level');
            }
            if (Schema::hasColumn('categories', 'order_level')) {
                $table->dropColumn('order_level');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->integer('level')->default(0);
            $table->integer('order_level')->default(0);
        });
    }
};
