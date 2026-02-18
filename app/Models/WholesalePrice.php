<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WholesalePrice extends Model
{
    protected $fillable = [
        'product_stock_id',
        'min_qty',
        'max_qty',
        'price',
    ];

    public function productStock()
    {
        return $this->belongsTo(ProductStock::class);
    }
}
