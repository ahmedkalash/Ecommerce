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
            $table->decimal('tax', 20, 2)->default(0.00)->after('discount_end_date');
            $table->string('tax_type', 20)->nullable()->after('tax');
            $table->decimal('weight', 20, 2)->default(0.00)->after('tax_type');
            $table->boolean('todays_deal')->default(0)->after('weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_stocks', function (Blueprint $table) {
            $table->dropColumn(['tax', 'tax_type', 'weight', 'todays_deal']);
        });
    }
};
