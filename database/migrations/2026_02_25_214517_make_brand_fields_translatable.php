<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Converts brands.name, meta_title, meta_description to JSON.
 *
 * Merges legacy brand_translations rows into the JSON column,
 * then drops the brand_translations table.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Step 1: Wrap existing values as {"en": ...} ────────────────────
        DB::statement('
            UPDATE brands
            SET
                name             = JSON_OBJECT("en", name),
                meta_title       = IF(meta_title IS NULL, NULL, JSON_OBJECT("en", meta_title)),
                meta_description = IF(meta_description IS NULL, NULL, JSON_OBJECT("en", meta_description))
        ');

        // ── Step 2: Change column types ────────────────────────────────────
        Schema::table('brands', function (Blueprint $table): void {
            $table->json('name')->change();
            $table->json('meta_title')->nullable()->change();
            $table->json('meta_description')->nullable()->change();
        });

        // ── Step 3: Merge legacy brand_translations ────────────────────────
        $translations = DB::table('brand_translations')->get();

        foreach ($translations as $translation) {
            $locale = $translation->lang;
            $brandId = $translation->brand_id;
            $translatedName = $translation->name;

            DB::statement('
                UPDATE brands
                SET name = JSON_SET(name, ?, ?)
                WHERE id = ?
                  AND JSON_EXTRACT(name, ?) IS NULL
            ', [
                "\$.{$locale}",
                $translatedName,
                $brandId,
                "\$.{$locale}",
            ]);
        }

        // ── Step 4: Drop legacy table ──────────────────────────────────────
        Schema::dropIfExists('brand_translations');
    }

    public function down(): void
    {
        // Recreate brand_translations table.
        Schema::create('brand_translations', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('brand_id');
            $table->string('name');
            $table->string('lang');
            $table->timestamps();
        });

        // Reverse: unwrap JSON back to plain English string.
        Schema::table('brands', function (Blueprint $table): void {
            $table->string('name', 255)->change();
            $table->string('meta_title', 255)->nullable()->change();
            $table->text('meta_description')->nullable()->change();
        });

        DB::statement('
            UPDATE brands
            SET
                name             = JSON_UNQUOTE(JSON_EXTRACT(name, "$.en")),
                meta_title       = JSON_UNQUOTE(JSON_EXTRACT(meta_title, "$.en")),
                meta_description = JSON_UNQUOTE(JSON_EXTRACT(meta_description, "$.en"))
        ');
    }
};
