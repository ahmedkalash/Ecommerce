<?php

namespace App\Models;

use App\Enums\SpecialPriceType;
use App\Observers\ProductStockObserver;
use App\Services\PricingResolverService;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\SchemalessAttributes\SchemalessAttributesTrait;

#[ObservedBy([ProductStockObserver::class])]
class ProductStock extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SchemalessAttributesTrait;

    protected $fillable = [
        'product_id',
        'variant',
        'sku',
        'price',
        'special_price',
        'special_price_type',
        'special_price_start',
        'special_price_end',
        'qty',
        'video_provider',
        'min_qty',
        'cash_on_delivery',
        'todays_deal',
        'extra_attributes',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'special_price' => 'decimal:2',
        'special_price_type' => SpecialPriceType::class,
        'special_price_start' => 'datetime',
        'special_price_end' => 'datetime',
    ];

    protected $schemalessAttributes = [
        'extra_attributes',
    ];

    /**
     * Check if the special price is currently active.
     */
    public function isSpecialPriceActive(): bool
    {
        return app(PricingResolverService::class)->isSpecialPriceActive($this);
    }

    /**
     * Scope to filter variants with currently active special prices.
     */
    public function scopeWithActiveSpecialPrice(Builder $query): Builder
    {
        return $query->whereNotNull('special_price')
            ->whereNotNull('special_price_type')
            ->whereNotNull('special_price_start')
            ->whereNotNull('special_price_end')
            ->where('special_price_start', '<=', now())
            ->where('special_price_end', '>=', now());
    }

    public function scopeWithExtraAttributes(): Builder
    {
        return $this->extra_attributes->modelScope();
    }

    /**
     * @return BelongsTo<Product, ProductStock>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function wholesalePrices()
    {
        return $this->hasMany(WholesalePrice::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')
            ->singleFile();

        $this->addMediaCollection('gallery');

        $this->addMediaCollection('files');

        $this->addMediaCollection('short_video')
            ->singleFile();

        $this->addMediaCollection('short_video_thumbnail')
            ->singleFile();

        $this->addMediaCollection('meta_img')
            ->singleFile();

        $this->addMediaCollection('pdf');
    }
}
