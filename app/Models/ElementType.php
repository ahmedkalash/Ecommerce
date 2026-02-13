<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class ElementType extends Model
{
    use PreventDemoModeChanges;

    protected $fillable = [
        'is_default',
    ];

    public function element()
    {
        return $this->belongsTo(Element::class);
    }

    public function image()
    {
        return $this->belongsTo(Media::class);
    }

    public function element_styles()
    {
        return $this->hasMany(ElementStyle::class);
    }
}
