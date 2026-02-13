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

    protected $with = ['product_translations', 'taxes', 'thumbnail'];

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

    public function thumbnail()
    {
        return $this->belongsTo(Media::class, 'thumbnail_img');
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

    // add gallery image to thumb

    // add gallery image to thumb
    // Old thumbnailImg removed here

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
     * Get the comma-separated URLs for the gallery images.
     * Prioritizes Spatie Media Library and falls back to legacy 'photos' column.
     */
    public function gallery(): Attribute
    {
        return Attribute::get(function ($value, $attributes) {
            $media = $this->getMedia('gallery');
            if ($media->isNotEmpty()) {
                return $media->map(fn ($item) => $item->getUrl())->implode(',');
            }

            return $attributes['photos'] ?? '';
        });
    }

    /**
     * Get the primary meta image URL.
     */
    public function metaImg(): Attribute
    {
        return Attribute::get(function ($value, $attributes) {
            $url = $this->getFirstMediaUrl('meta');
            if ($url) {
                return $url;
            }

            // If it's a numeric ID from legacy, we wrap it in a helper check
            if (is_numeric($value)) {
                return get_file_by_id($value);
            }

            return $value;
        });
    }

    /**
     * Get the product thumbnail URL.
     * Prioritizes 'thumbnail' collection, then first photo from gallery, then placeholder.
     */
    public function thumbnailImg(): Attribute
    {
        return Attribute::get(function ($value, $attributes) {
            $mediaUrl = $this->getFirstMediaUrl('thumbnail');
            if ($mediaUrl) {
                return $mediaUrl;
            }

            // Fallback to specific legacy ID if exists
            if (isset($attributes['thumbnail_img']) && is_numeric($attributes['thumbnail_img'])) {
                return get_file_by_id($attributes['thumbnail_img']);
            }

            // Fallback to first image in photos list
            $photos = $attributes['photos'] ?? null;
            if ($photos) {
                $photosArray = explode(',', $photos);
                if (count($photosArray) > 0) {
                    $firstPhotoId = $photosArray[0];
                    if (is_numeric($firstPhotoId)) {
                        return get_file_by_id($firstPhotoId);
                    }

                    $legacyPath = 'uploads/all/'.$firstPhotoId;
                    if (file_exists(public_path($legacyPath))) {
                        return static_asset($legacyPath);
                    }
                }
            }

            return static_asset('assets/img/placeholder.jpg');
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
