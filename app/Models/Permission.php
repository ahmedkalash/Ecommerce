<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Translatable\HasTranslations;

/**
 * Custom Permission model extending Spatie Laravel Permission's Permission.
 *
 * The `name` column remains a plain string — Spatie Permission uses it
 * internally for gate/guard resolution and must NOT be translatable.
 *
 * The `label` JSON column holds human-readable display names per locale,
 * e.g. {"en": "Edit products", "ar": "تعديل المنتجات"}.
 *
 * @property string $name The technical identifier (unchanged).
 * @property string $display_name Computed: label for current locale, fallback to name.
 */
class Permission extends SpatiePermission
{
    use HasTranslations;

    /**
     * Fields stored as JSON for per-locale display labels.
     */
    public array $translatable = ['label'];

    /**
     * Get the human-readable display name for the current locale.
     * Falls back to the technical `name` if no label is set for the locale.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->label ?? $this->name;
    }
}
