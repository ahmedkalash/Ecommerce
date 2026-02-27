<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreorderProductTranslation extends Model
{
    use HasFactory,PreventDemoModeChanges;

    protected $fillable = ['preorder_product_id', 'product_name', 'unit', 'description', 'lang'];

    public function preorderProduct()
    {
        return $this->belongsTo(PreorderProduct::class);
    }
}
