<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\SchemalessAttributes\SchemalessAttributesTrait;

class ProductStock extends Model implements HasMedia
{
    use InteractsWithMedia, SchemalessAttributesTrait;

    protected $fillable = [
        'product_id',
        'variant',
        'sku',
        'price',
        'qty',
        'video_provider',
        'min_qty',
        'cash_on_delivery',
        'todays_deal',
        'extra_attributes',
    ];

    protected $schemalessAttributes = [
        'extra_attributes',
    ];

    public function scopeWithExtraAttributes(): Builder
    {
        return $this->extra_attributes->modelScope();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
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
