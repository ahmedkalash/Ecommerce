<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use PreventDemoModeChanges;

    public function product_taxes()
    {
        return $this->hasMany(ProductTax::class);
    }
}
