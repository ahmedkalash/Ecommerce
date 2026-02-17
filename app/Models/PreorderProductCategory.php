<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreorderProductCategory extends Model
{
    use HasFactory,PreventDemoModeChanges;

    public function preorderProduct()
    {
        return $this->belongsTo(PreorderProduct::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
