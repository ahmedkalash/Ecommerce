<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/** placeholder model for general uploaded assets.*/
class App extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'app';

    public $timestamps = false;

    protected $fillable = ['id'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('uploads')
            ->useDisk('public');
    }
}
