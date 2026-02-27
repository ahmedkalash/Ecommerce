<?php

namespace App\Enums\Coupons;

enum CouponTypes: string
{
    case CART_BASED = 'cart_based';
    case PRODUCT_BASED = 'product_based';
}
