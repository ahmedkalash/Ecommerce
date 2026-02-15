<?php

namespace App\Models;

use App;
use App\Models\Traits\Product\ProductRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, ProductRelationships;

    protected $guarded = [];

    protected $casts = [
        //
    ];

    protected $with = [
        'product_translations', 'taxes', 'media', 'stocks', 'main_category', 'brand',
    ]; // Added stocks, main_category, brand to eager load if commonly used

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

        $this->addMediaCollection('pdf')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf']);

        $this->addMediaCollection('short_video')
            ->singleFile()
            ->acceptsMimeTypes(['video/mp4', 'video/webm']);

        $this->addMediaCollection('video_thumbnail')
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

    // Stocks relationship
    public function stocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    // Since we are Strict Schema, we do NOT provide getPriceAttribute accessor on Product.
    // Frontend/API must request stocks->first()->price etc.

}
