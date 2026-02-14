<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Convert root-level categories from parent_id = 0 to parent_id = NULL.
     * This aligns with Laravel's convention for representing root nodes
     * in hierarchical (parent-child) relationships, and is required
     * by the filament-select-tree plugin to correctly build the tree.
     */
    public function up(): void
    {
        DB::table('categories')
            ->where('parent_id', 0)
            ->update(['parent_id' => null]);
    }

    /**
     * Reverse the migrations.
     *
     * Revert root-level categories from parent_id = NULL to parent_id = 0.
     */
    public function down(): void
    {
        DB::table('categories')
            ->whereNull('parent_id')
            ->update(['parent_id' => 0]);
    }
};
