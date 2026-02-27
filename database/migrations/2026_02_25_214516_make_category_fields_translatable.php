<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Converts categories.name, meta_title, meta_description to JSON.
 *
 * Data migration strategy:
 *  1. Wrap the existing English value in each JSON column as {"en": "<value>"}.
 *  2. Merge translations from the legacy category_translations table
 *     (rows with 'lang' key → locale key in JSON).
 *  3. Drop the legacy category_translations table.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Step 1: Wrap existing values as {"en": ...} ────────────────────
        DB::statement('
            UPDATE categories
            SET
                name             = JSON_OBJECT("en", name),
                meta_title       = IF(meta_title IS NULL, NULL, JSON_OBJECT("en", meta_title)),
                meta_description = IF(meta_description IS NULL, NULL, JSON_OBJECT("en", meta_description))
        ');

        // ── Step 2: Change column types ────────────────────────────────────
        Schema::table('categories', function (Blueprint $table): void {
            $table->json('name')->change();
            $table->json('meta_title')->nullable()->change();
            $table->json('meta_description')->nullable()->change();
        });

        // ── Step 3: Merge legacy category_translations into JSON column ────
        // For each row in category_translations, add the translated name under
        // the locale key (lang field) in the parent categories.name JSON.
        $translations = DB::table('category_translations')->get();

        foreach ($translations as $translation) {
            $locale = $translation->lang;
            $categoryId = $translation->category_id;
            $translatedName = $translation->name;

            // Only set if the locale key is not already present (en already set above).
            DB::statement('
                UPDATE categories
                SET name = JSON_SET(name, ?, ?)
                WHERE id = ?
                  AND JSON_EXTRACT(name, ?) IS NULL
            ', [
                "\$.{$locale}",
                $translatedName,
                $categoryId,
                "\$.{$locale}",
            ]);
        }

        // ── Step 4: Drop legacy table ──────────────────────────────────────
        Schema::dropIfExists('category_translations');
    }

    public function down(): void
    {
        // Recreate category_translations table.
        Schema::create('category_translations', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id');
            $table->string('name');
            $table->string('lang');
            $table->timestamps();
        });

        // Reverse: unwrap JSON back to plain English string.
        Schema::table('categories', function (Blueprint $table): void {
            $table->string('name', 255)->change();
            $table->string('meta_title', 255)->nullable()->change();
            $table->text('meta_description')->nullable()->change();
        });

        DB::statement('
            UPDATE categories
            SET
                name             = JSON_UNQUOTE(JSON_EXTRACT(name, "$.en")),
                meta_title       = JSON_UNQUOTE(JSON_EXTRACT(meta_title, "$.en")),
                meta_description = JSON_UNQUOTE(JSON_EXTRACT(meta_description, "$.en"))
        ');
    }
};
