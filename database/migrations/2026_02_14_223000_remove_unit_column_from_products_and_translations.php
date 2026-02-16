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
            if (Schema::hasColumn('products', 'unit')) {
                $table->dropColumn('unit');
            }
        });

        Schema::table('product_translations', function (Blueprint $table) {
            if (Schema::hasColumn('product_translations', 'unit')) {
                $table->dropColumn('unit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'unit')) {
                $table->string('unit', 20)->nullable();
            }
        });

        Schema::table('product_translations', function (Blueprint $table) {
            if (! Schema::hasColumn('product_translations', 'unit')) {
                $table->string('unit', 20)->nullable();
            }
        });
    }
};
