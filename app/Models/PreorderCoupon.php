<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreorderCoupon extends Model
{
    use HasFactory,PreventDemoModeChanges;

    public function preorder_product()
    {
        return $this->belongsTo(PreorderProduct::class, 'preorder_product_id');
    }
}
