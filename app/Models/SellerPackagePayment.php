<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class SellerPackagePayment extends Model
{
    use PreventDemoModeChanges;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seller_package()
    {
        return $this->belongsTo(SellerPackage::class);
    }
}
