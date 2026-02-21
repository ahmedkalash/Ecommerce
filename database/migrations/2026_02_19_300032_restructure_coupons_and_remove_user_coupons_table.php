<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop the old table (Warning: Ensure data is backed up or migrated first!)
        Schema::dropIfExists('user_coupons');

        // 2. Drop columns in a dedicated block to prevent SQL conflicts
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'discount_type', 'type', 'details']);
        });

        // 3. Add the new columns and modify existing ones
        Schema::table('coupons', function (Blueprint $table) {
            $table->enum('type', ['cart_based', 'product_based'])->after('id');
            $table->enum('discount_type', ['fixed', 'percentage'])->after('type');

            $table->decimal('min_money_spent', 10, 2)->nullable();
            $table->decimal('max_discount', 10, 2)->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);

            $table->json('product_ids')->nullable();

            $table->string('label')->after('code');

            // Assuming you have doctrine/dbal installed to use ->change()
            $table->unique('code');
            $table->dateTime('start_date')->change();
            $table->dateTime('end_date')->change();
            $table->decimal('discount', 10, 2)->change(); // Always define precision for money
        });

        // 4. Update the usages table
        Schema::table('coupon_usages', function (Blueprint $table) {
            // Add order tracking for audit trails
            $table->integer('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();

            // Add foreign key constraint to existing coupon_id
            $table->foreign('coupon_id')->references('id')->on('coupons')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('coupon_usages', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['coupon_id']);
            $table->dropColumn('order_id');
        });

        // 1. Drop the columns we added in up()
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn([
                'type',
                'discount_type',
                'min_money_spent',
                'max_discount',
                'usage_limit',
                'used_count',
                'product_ids',
                'label',
            ]);

            $table->dropUnique(['code']); // Remove the unique index
        });

        // 2. Re-add the legacy columns
        Schema::table('coupons', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('discount_type');
            $table->string('type');
            $table->text('details')->nullable();

            // Note: Reverting ->change() is inherently difficult in Laravel.
            // Often, developers leave the datetime ->change() as is during rollback
            // because reverting to 'date' can cause data truncation errors.
        });

        // 3. Recreate the legacy pivot
        Schema::create('user_coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }
};
