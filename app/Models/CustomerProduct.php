<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CustomerProduct extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $with = ['customer_product_translations'];

    public function getTranslation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $customer_product_translations = $this->customer_product_translations->where('lang', $lang)->first();

        return $customer_product_translations != null ? $customer_product_translations->$field : $this->$field;
    }

    public function scopeIsActiveAndApproval($query)
    {
        return $query->where('status', '1')
            ->where('published', '1');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function subsubcategory()
    {
        return $this->belongsTo(SubSubCategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function customer_product_translations()
    {
        return $this->hasMany(CustomerProductTranslation::class);
    }

    /**
     * Register Spatie media collections for classified products.
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
    }

    /**
     * Get the product thumbnail URL.
     * Prioritizes 'thumbnail' collection, then first gallery image, then placeholder.
     */
    public function thumbnailImg(): Attribute
    {
        return Attribute::get(function () {
            $thumbnailUrl = $this->getFirstMediaUrl('thumbnail');
            if ($thumbnailUrl) {
                return $thumbnailUrl;
            }

            $firstGallery = $this->getFirstMediaUrl('gallery');
            if ($firstGallery) {
                return $firstGallery;
            }

            return static_asset('assets/img/placeholder.jpg');
        });
    }

    /**
     * Get all gallery media items.
     */
    public function galleryMedia(): \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection
    {
        return $this->getMedia('gallery');
    }

    /**
     * Get the primary meta image URL.
     */
    public function metaImg(): Attribute
    {
        return Attribute::get(function () {
            return $this->getFirstMediaUrl('meta') ?: null;
        });
    }

    /**
     * Get the PDF file URL.
     */
    public function pdfUrl(): Attribute
    {
        return Attribute::get(function () {
            return $this->getFirstMediaUrl('pdf') ?: null;
        });
    }
}
