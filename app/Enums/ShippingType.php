<?php

namespace App\Enums;

enum ShippingType: string
{
    case FLAT_RATE = 'flat_rate';

    case FREE_SHIPPING = 'free_shipping';

    case AREA_WISE_SHIPPING = 'area_wise_shipping';

    case PRODUCT_WISE_SHIPPING = 'product_wise_shipping';
}
