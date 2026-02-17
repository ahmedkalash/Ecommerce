<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class SellerPackageTranslation extends Model
{
    use PreventDemoModeChanges;

    protected $fillable = ['name', 'lang', 'seller_package_id'];

    public function seller_package()
    {
        return $this->belongsTo(SellerPackage::class);
    }
}
