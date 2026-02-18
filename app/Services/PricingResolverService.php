<?php

namespace App\Services;

use App\DataTransferObjects\PriceResult;
use App\Enums\SpecialPriceType;
use App\Models\ProductStock;

/**
 * Resolves the final customer-facing price for a product variant.
 *
 * This is a read-only service — it does NOT mutate data.
 * Any consumer (Cart, API, Filament, Blade views) should use this
 * service as the single source of truth for pricing.
 */
class PricingResolverService
{
    /**
     * Resolve the final price for a variant, applying any active special price.
     */
    public function resolve(ProductStock $variant): PriceResult
    {
        $basePrice = (float) $variant->price;
        if (! $this->isSpecialPriceActive($variant)) {
            return new PriceResult(
                basePrice: $basePrice,
                finalPrice: $basePrice,
                discountAmount: 0,
                isSpecialActive: false,
                specialPriceType: null,
            );
        }

        $finalPrice = round($this->calculateSpecialPrice($variant), 2);

        return new PriceResult(
            basePrice: $basePrice,
            finalPrice: $finalPrice,
            discountAmount: round($basePrice - $finalPrice, 2),
            isSpecialActive: true,
            specialPriceType: $variant->special_price_type
        );
    }

    /**
     * Check if a variant has an active special price right now.
     */
    public function isSpecialPriceActive(ProductStock $variant): bool
    {
        return $variant->special_price != null &&
            $variant->special_price_type != null &&
            $this->isWithinDateRange($variant);
    }

    /**
     * Check if the current time falls within the special price date window.
     *
     * If both dates are null, the special price is NOT active (requires explicit dates).
     */
    private function isWithinDateRange(ProductStock $variant): bool
    {
        if ($variant->special_price_start === null || $variant->special_price_end === null) {
            return false;
        }

        return now()->between($variant->special_price_start, $variant->special_price_end);
    }

    /**
     * Calculate the special price based on the type.
     *
     * - DiscountPercent: base - (base * value / 100)
     * - FixedPrice: the value IS the final price
     */
    private function calculateSpecialPrice(ProductStock $variant): float
    {
        $basePrice = (float) $variant->price;
        $specialValue = (float) $variant->special_price;

        return match ($variant->special_price_type) {
            SpecialPriceType::DiscountPercent => $basePrice - ($basePrice * $specialValue / 100),
            SpecialPriceType::FixedPrice => $specialValue,
            default => $basePrice,
        };
    }
}
