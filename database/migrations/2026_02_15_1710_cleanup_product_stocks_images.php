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
        if (Schema::hasColumn('product_stocks', 'image')) {
            Schema::table('product_stocks', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('product_stocks', 'image')) {
            Schema::table('product_stocks', function (Blueprint $table) {
                $table->string('image')->nullable();
            });
        }
    }
};
