<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use PreventDemoModeChanges;

    protected $with = ['user', 'user.shop'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function seller_package()
    {
        return $this->belongsTo(SellerPackage::class);
    }
}
