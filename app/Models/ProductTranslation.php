<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{
    use PreventDemoModeChanges;

    protected $fillable = ['product_id', 'name', 'description', 'lang'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
