<?php

namespace App\Models;

use App;
use App\Models\Traits\Product\ProductRelationships;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\SchemalessAttributes\SchemalessAttributesTrait;
use Spatie\Tags\HasTags;

class Product extends Model implements HasMedia
{
    use HasFactory, HasTags, InteractsWithMedia, ProductRelationships, SchemalessAttributesTrait;

    protected $fillable = [
        'name',
        'added_by',
        'user_id',
        'brand_id',
        'description',
        'published',
        'approved',
        'featured',
        'featured',
        'todays_deal',
        'cash_on_delivery',
        'shipping_type',
        'shipping_cost',
        'est_shipping_days',
        'meta_title',
        'meta_description',
        'slug',
        'rating',
        'barcode',
        'digital',
        'file_name',
        'file_path',
        'external_link',
        'external_link_btn',
        'wholesale_product',
        'frequently_bought_selection_type',
        'has_warranty',
        'warranty_id',
        'warranty_note_id',
        'extra_attributes',
    ];

    protected $schemalessAttributes = [
        'extra_attributes',
    ];

    public function scopeWithExtraAttributes(): Builder
    {
        return $this->extra_attributes->modelScope();
    }

    protected $casts = [
        'published' => 'boolean',
        'approved' => 'boolean',
        'featured' => 'boolean',
        'digital' => 'boolean',
        'wholesale_product' => 'boolean',
        'has_warranty' => 'boolean',
        'shipping_cost' => 'float',
        'est_shipping_days' => 'integer',
    ];

    protected $with = [
        'product_translations',
        'taxes',
        'media',
        'stocks',
        'categories',
        'brand',
    ];

    public function getTranslation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $product_translations = $this->product_translations->where('lang', $lang)->first();

        return $product_translations != null ? $product_translations->$field : $this->$field;
    }

    public function scopePhysical($query)
    {
        return $query->where('digital', 0);
    }

    public function scopeDigital($query)
    {
        return $query->where('digital', 1);
    }

    public function scopeIsApprovedPublished($query)
    {
        return $query->where('approved', '1')->where('published', 1);
    }

    /**
     * Register specifically named media collections for Products.
     * This provides a "Source of Truth" for what each image represents.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')
            ->singleFile()
            ->useFallbackUrl(static_asset('assets/img/placeholder.jpg'));

        $this->addMediaCollection('gallery');

        $this->addMediaCollection('meta')
            ->singleFile();
    }

    /**
     * Get the gallery media collection (Spatie MediaCollection).
     * Use this when you need to iterate over individual media items.
     */
    public function galleryMedia(): \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection
    {
        return $this->getMedia('gallery');
    }

    // Since we are Strict Schema, we do NOT provide getPriceAttribute accessor on Product.
    // Frontend/API must request stocks->first()->price etc.

}
