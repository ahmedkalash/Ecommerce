<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Brand extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia, Searchable;

    /**
     * Fields stored as JSON, served per active locale via spatie/laravel-translatable.
     */
    public array $translatable = ['name', 'meta_title', 'meta_description'];

    protected $fillable = ['name', 'logo', 'slug', 'meta_title', 'meta_description'];

    /**
     * Build the array that Meilisearch indexes for this model.
     */
    public function toSearchableArray(): array
    {
        $data = ['id' => $this->id];

        foreach (array_keys($this->getTranslations('name')) as $locale) {
            $data["name_{$locale}"] = $this->getTranslation('name', $locale, false);
        }

        return $data;
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
