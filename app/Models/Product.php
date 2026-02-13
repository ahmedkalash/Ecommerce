<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = ['choice_attributes'];

    protected $with = ['product_translations', 'taxes', 'media'];

    public function getTranslation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $product_translations = $this->product_translations->where('lang', $lang)->first();

        return $product_translations != null ? $product_translations->$field : $this->$field;
    }

    public function product_translations()
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function main_category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function frequently_bought_products()
    {
        return $this->hasMany(FrequentlyBoughtProduct::class);
    }

    public function product_categories()
    {
        return $this->hasMany(ProductCategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function product_queries()
    {
        return $this->hasMany(ProductQuery::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function stocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function taxes()
    {
        return $this->hasMany(ProductTax::class);
    }

    public function flash_deal_products()
    {
        return $this->hasMany(FlashDealProduct::class);
    }

    public function bids()
    {
        return $this->hasMany(AuctionProductBid::class);
    }

    public function scopePhysical($query)
    {
        return $query->where('digital', 0);
    }

    public function scopeDigital($query)
    {
        return $query->where('digital', 1);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function scopeIsApprovedPublished($query)
    {
        return $query->where('approved', '1')->where('published', 1);
    }

    public function last_viewed_products()
    {
        return $this->hasMany(LastViewedProduct::class);
    }

    public function warranty()
    {
        return $this->belongsTo(Warranty::class);
    }

    public function warrantyNote()
    {
        return $this->belongsTo(Note::class, 'warranty_note_id');
    }

    public function refundNote()
    {
        return $this->belongsTo(Note::class, 'refund_note_id');
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

    /**
     * Get gallery image URLs as a comma-separated string.
     * Accessed as $product->gallery.
     */
    public function gallery(): Attribute
    {
        return Attribute::get(function () {
            $media = $this->getMedia('gallery');

            return $media->isNotEmpty()
                ? $media->map(fn ($item) => $item->getUrl())->implode(',')
                : '';
        });
    }

    /**
     * Backward-compatible accessor: $product->photos returns gallery URLs.
     * Alias of gallery() for views that still reference ->photos.
     */
    public function photos(): Attribute
    {
        return Attribute::get(fn () => $this->gallery);
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
     * Get the short video URL.
     */
    public function shortVideo(): Attribute
    {
        return Attribute::get(function () {
            return $this->getFirstMediaUrl('short_video') ?: null;
        });
    }

    /**
     * Get the short video thumbnail URL.
     */
    public function shortVideoThumbnail(): Attribute
    {
        return Attribute::get(function () {
            return $this->getFirstMediaUrl('video_thumbnail') ?: null;
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

    protected function videoLink(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: function ($value) {
                if (! is_array($value)) {
                    return null;
                }
                $filtered = array_filter($value, function ($item) {
                    return trim($item) !== '';
                });

                return empty($filtered) ? null : json_encode($filtered);
            },
        );
    }
}
