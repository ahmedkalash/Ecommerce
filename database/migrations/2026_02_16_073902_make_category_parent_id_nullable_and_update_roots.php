<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Make parent_id nullable
        Schema::table('categories', function (Blueprint $table) {
            $table->integer('parent_id')->nullable()->change();
        });

        // 2. Update existing root categories (0 -> NULL)
        DB::table('categories')->where('parent_id', 0)->update(['parent_id' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Update NULLs back to 0
        DB::table('categories')->whereNull('parent_id')->update(['parent_id' => 0]);

        // 2. Make parent_id not nullable (reverting to integer default 0 if that was the state, usually just integer)
        // Note: Default was likely not set in original schema, but we can't easily revert 'change()' without raw SQL if we want exact previous state.
        // Assuming we just want to revert the nullable flag.
        Schema::table('categories', function (Blueprint $table) {
            $table->integer('parent_id')->nullable(false)->default(0)->change();
        });
    }
};
