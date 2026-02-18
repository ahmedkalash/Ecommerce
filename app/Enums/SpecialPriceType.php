<?php

namespace App\Enums;

enum SpecialPriceType: string
{
    case DiscountPercent = 'discount_percent';
    case FixedPrice = 'fixed_price';

    public function label(): string
    {
        return match ($this) {
            self::DiscountPercent => 'Discount Percentage',
            self::FixedPrice => 'Fixed Price',
        };
    }
}
