<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProductStock extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'product_id',
        'variant',
        'sku',
        'price',
        'qty',
        'video_link',
        'video_provider',
        'min_qty',
        'cash_on_delivery',
    ];

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

        $this->addMediaCollection('video_thumbnail')
            ->singleFile();

        $this->addMediaCollection('meta_img')
            ->singleFile();

        $this->addMediaCollection('pdf');
    }
}
