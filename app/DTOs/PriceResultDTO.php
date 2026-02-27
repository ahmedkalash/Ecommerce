<?php

namespace App\DTOs;

use App\Enums\SpecialPriceType;

/**
 * Immutable value object representing a resolved variant price.
 *
 * Returned by PricingResolverService::resolve().
 */
readonly class PriceResultDTO
{
    public function __construct(
        public float $basePrice,
        public float $finalPrice,
        public float $discountAmount,
        public bool $isSpecialActive = false,
        public ?SpecialPriceType $specialPriceType = null,
    ) {}
}
