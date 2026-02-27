<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model
{
    use PreventDemoModeChanges;

    protected $fillable = [
        'user_id',
        'coupon_id',
        'order_id',
        'discount_amount',
    ];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
