<?php

namespace App\DataTransferObjects;

use App\Enums\SpecialPriceType;

/**
 * Immutable value object representing a resolved variant price.
 *
 * Returned by PricingResolverService::resolve().
 */
readonly class PriceResult
{
    public function __construct(
        public float $basePrice,
        public float $finalPrice,
        public float $discountAmount,
        public bool $isSpecialActive = false,
        public ?SpecialPriceType $specialPriceType = null,
    ) {}
}
