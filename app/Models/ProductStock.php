<?php

namespace App\Models;

use App\Enums\SpecialPriceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProductStock extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'product_id',
        'variant',
        'sku',
        'price',
        'qty',
        'image',
        'min_qty',
        'cash_on_delivery',
        'todays_deal',
        'special_price',
        'special_price_type',
        'special_price_start',
        'special_price_end',
        'extra_attributes',
    ];

    protected $casts = [
        'price' => 'float',
        'qty' => 'integer',
        'min_qty' => 'integer',
        'cash_on_delivery' => 'boolean',
        'todays_deal' => 'boolean',
        'special_price' => 'float',
        'special_price_type' => SpecialPriceType::class,
        'special_price_start' => 'datetime',
        'special_price_end' => 'datetime',
        'extra_attributes' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')
            ->singleFile()
            ->useFallbackUrl(static_asset('assets/img/placeholder.jpg'));

        $this->addMediaCollection('gallery');

        $this->addMediaCollection('meta')
            ->singleFile();
    }
}
