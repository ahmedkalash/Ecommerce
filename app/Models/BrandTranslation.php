<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class BrandTranslation extends Model
{
    use PreventDemoModeChanges;

    protected $fillable = ['name', 'lang', 'brand_id'];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
