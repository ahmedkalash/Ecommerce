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
            $table->schemalessAttributes('extra_attributes');
        });

        Schema::table('product_stocks', function (Blueprint $table) {
            $table->schemalessAttributes('extra_attributes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('extra_attributes');
        });

        Schema::table('product_stocks', function (Blueprint $table) {
            $table->dropColumn('extra_attributes');
        });
    }
};
