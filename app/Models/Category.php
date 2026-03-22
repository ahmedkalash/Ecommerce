<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class Category extends Model implements HasMedia
{
    use HasFactory, HasRecursiveRelationships, HasTranslations, InteractsWithMedia, Searchable;

    protected $guarded = [];

    /**
     * Fields stored as JSON, served per active locale via spatie/laravel-translatable.
     */
    public array $translatable = ['name', 'meta_title', 'meta_description'];

    /** @deprecated category_translations table has been dropped — translations now in JSON columns. */
    protected $with = [];

    public function toSearchableArray(): array
    {
        $data = ['id' => $this->id];

        foreach (array_keys($this->getTranslations('name')) as $locale) {
            $data["name_{$locale}"] = $this->getTranslation('name', $locale, false);
        }
        foreach (array_keys($this->getTranslations('description')) as $locale) {
            $data["description_{$locale}"] = $this->getTranslation('description', $locale, false);
        }
        foreach (array_keys($this->getTranslations('meta_title')) as $locale) {
            $data["meta_title_{$locale}"] = $this->getTranslation('meta_title', $locale, false);
        }

        return $data;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('banner')
            ->singleFile();

        $this->addMediaCollection('icon')
            ->singleFile();

        $this->addMediaCollection('cover_image')
            ->singleFile();
    }

    public function getParentIdName(): string
    {
        return 'parent_id';
    }

    public function coverImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_image');
    }

    public function catIcon(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'icon');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_categories');
    }

    public function preorderProducts(): BelongsToMany
    {
        return $this->belongsToMany(PreorderProduct::class, 'preorder_product_categories');
    }

    public function bannerImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'banner');
    }

    public function classified_products(): HasMany
    {
        return $this->hasMany(CustomerProduct::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function childrenCategories(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->with('categories');
    }

    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function sizeChart(): BelongsTo
    {
        return $this->belongsTo(SizeChart::class, 'id', 'category_id');
    }

    public function sellerDiscount(): HasOne
    {
        return $this->hasOne(SellerCategory::class)->where('seller_id', auth()->id());
    }

    public function sellerDiscounts(): HasMany
    {
        return $this->hasMany(SellerCategory::class);
    }
}
