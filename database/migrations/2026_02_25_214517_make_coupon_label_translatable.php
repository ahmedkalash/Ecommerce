<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Converts coupons.label from varchar(191) to JSON for spatie/laravel-translatable.
 * Wraps existing values as {"en": "<value>"}.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Step 1: Wrap existing values as {"en": ...} ────────────────────
        DB::statement('UPDATE coupons SET label = JSON_OBJECT("en", label)');

        // ── Step 2: Change column type ─────────────────────────────────────
        Schema::table('coupons', function (Blueprint $table): void {
            $table->json('label')->change();
        });
    }

    public function down(): void
    {
        // Reverse: shrink back to varchar, unwrap English value.
        Schema::table('coupons', function (Blueprint $table): void {
            $table->string('label', 191)->change();
        });

        DB::statement('UPDATE coupons SET label = JSON_UNQUOTE(JSON_EXTRACT(label, "$.en"))');
    }
};
