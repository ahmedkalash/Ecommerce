<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class CartProduct extends Model
{
    use PreventDemoModeChanges;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
