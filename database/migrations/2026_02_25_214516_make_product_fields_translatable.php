<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Converts the following products columns from plain strings to JSON for
 * spatie/laravel-translatable:  name, description, meta_title, meta_description.
 *
 * Data migration: wraps existing values as {"en": "<value>"} so all existing
 * records have at least an English translation after the migration runs.
 *
 * Also drops the now-redundant products_name_index B-tree index (it was already
 * ineffective for LIKE '%x%' searches; full-text search will be handled by Scout).
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Step 1: Convert plain strings to JSON inline ───────────────────
        // We work around MySQL's strict type enforcement by first setting every
        // column to a JSON-safe string, then altering the column type.

        DB::statement('
            UPDATE products
            SET
                name             = JSON_OBJECT("en", name),
                description      = JSON_OBJECT("en", description),
                meta_title       = IF(meta_title IS NULL,   NULL, JSON_OBJECT("en", meta_title)),
                meta_description = IF(meta_description IS NULL, NULL, JSON_OBJECT("en", meta_description))
        ');

        // ── Step 2: Alter column types ─────────────────────────────────────
        Schema::table('products', function (Blueprint $table): void {
            // Drop the now-useless B-tree index before changing the column type.
            $table->dropIndex('products_name_index');

            $table->json('name')->change();
            $table->json('description')->nullable()->change();
            $table->json('meta_title')->nullable()->change();
            $table->json('meta_description')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Reverse: unwrap JSON back to plain English string.
        Schema::table('products', function (Blueprint $table): void {
            $table->string('name', 200)->change();
            $table->text('description')->nullable()->change();
            $table->text('meta_title')->nullable()->change();
            $table->text('meta_description')->nullable()->change();

            // Re-add the name index.
            $table->index('name', 'products_name_index');
        });

        DB::statement('
            UPDATE products
            SET
                name             = JSON_UNQUOTE(JSON_EXTRACT(name, "$.en")),
                description      = JSON_UNQUOTE(JSON_EXTRACT(description, "$.en")),
                meta_title       = JSON_UNQUOTE(JSON_EXTRACT(meta_title, "$.en")),
                meta_description = JSON_UNQUOTE(JSON_EXTRACT(meta_description, "$.en"))
        ');
    }
};
