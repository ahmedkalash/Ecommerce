<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds a nullable JSON `label` column to roles and permissions.
 *
 * The `name` column is intentionally left unchanged — it is used internally
 * by Spatie Laravel Permission for gate/guard resolution and must remain
 * a plain string.
 *
 * The `label` column holds human-readable display names per locale,
 * e.g. {"en": "Administrator", "ar": "مدير"}.
 *
 * Existing rows are seeded with {"en": <name>} so they always have a fallback.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table): void {
            $table->json('label')->nullable()->after('name');
        });

        Schema::table('permissions', function (Blueprint $table): void {
            $table->json('label')->nullable()->after('name');
        });

        // Seed existing rows with English label matching the current name.
        DB::statement('UPDATE roles SET label = JSON_OBJECT("en", name)');
        DB::statement('UPDATE permissions SET label = JSON_OBJECT("en", name)');
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table): void {
            $table->dropColumn('label');
        });

        Schema::table('permissions', function (Blueprint $table): void {
            $table->dropColumn('label');
        });
    }
};
