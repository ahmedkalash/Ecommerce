<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY user_type ENUM('admin', 'staff', 'customer', 'seller', 'delivery_boy') DEFAULT 'customer'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY user_type VARCHAR(50) DEFAULT 'customer'");
    }
};
