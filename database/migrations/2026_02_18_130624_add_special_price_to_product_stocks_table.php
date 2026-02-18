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
            $table->decimal('special_price', 20, 2)->unsigned()->nullable()->after('price');
            $table->enum('special_price_type', ['discount_percent', 'fixed_price'])->nullable()->after('special_price');
            $table->timestamp('special_price_start')->nullable()->after('special_price_type');
            $table->timestamp('special_price_end')->nullable()->after('special_price_start');

            $table->index(['special_price_start', 'special_price_end'], 'idx_special_price_dates');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_stocks', function (Blueprint $table) {
            $table->dropIndex('idx_special_price_dates');
            $table->dropColumn(['special_price', 'special_price_type', 'special_price_start', 'special_price_end']);
        });
    }
};
