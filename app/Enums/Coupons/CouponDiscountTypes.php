<?php

namespace App\Enums\Coupons;

enum CouponDiscountTypes: string
{
    case FIXED = 'fixed';
    case PERCENTAGE = 'percentage';

}
