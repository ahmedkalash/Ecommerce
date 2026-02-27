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
        Schema::table('product_stocks', function (Blueprint $table) {
            $table->decimal('discount', 20, 2)->default(0.00)->after('price');
            $table->string('discount_type', 20)->nullable()->after('discount');
            $table->integer('discount_start_date')->nullable()->after('discount_type');
            $table->integer('discount_end_date')->nullable()->after('discount_start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_stocks', function (Blueprint $table) {
            $table->dropColumn(['discount', 'discount_type', 'discount_start_date', 'discount_end_date']);
        });
    }
};
