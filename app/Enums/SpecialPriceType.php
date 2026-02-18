<?php

namespace App\Enums;

/**
 * Defines how the special_price value on a ProductStock is interpreted.
 *
 * - DiscountPercent: special_price is a percentage (0–100) subtracted from base price.
 * - FixedPrice:       special_price IS the final customer-facing price.
 */
enum SpecialPriceType: string
{
    case DiscountPercent = 'discount_percent';
    case FixedPrice = 'fixed_price';
}
