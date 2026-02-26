<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Translatable\HasTranslations;

/**
 * Custom Role model extending Spatie Laravel Permission's Role.
 *
 * The `name` column remains a plain string — Spatie Permission uses it
 * internally for gate/guard resolution and must NOT be translatable.
 *
 * The `label` JSON column holds human-readable display names per locale,
 * e.g. {"en": "Administrator", "ar": "مدير النظام"}.
 *
 * @property string $name The technical identifier (unchanged).
 * @property string $display_name Computed: label for current locale, fallback to name.
 */
class Role extends SpatieRole
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
        $locale = app()->getLocale();
        $label = $this->getTranslation('label', $locale, false);

        return $label ?: $this->name;
    }
}
