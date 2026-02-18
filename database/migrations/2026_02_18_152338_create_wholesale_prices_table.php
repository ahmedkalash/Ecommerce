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
        // Drop if exists to handle failed attempts
        Schema::dropIfExists('wholesale_prices');

        Schema::create('wholesale_prices', function (Blueprint $group) {
            $group->id();
            // Matching legacy int(11) from product_stocks table
            $group->integer('product_stock_id');
            $group->integer('min_qty');
            $group->integer('max_qty');
            $group->double('price', 20, 2);
            $group->timestamps();

            $group->foreign('product_stock_id')->references('id')->on('product_stocks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wholesale_prices');
    }
};
