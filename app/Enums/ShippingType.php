<?php

namespace App\Enums;

enum ShippingType: string
{
    case FLAT_RATE = 'flat_rate';
    case FREE_SHIPPING = 'free_shipping';
}
