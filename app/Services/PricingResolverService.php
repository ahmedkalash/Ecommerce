<?php

namespace App\Services;

use App\DataTransferObjects\PriceResult;
use App\Enums\SpecialPriceType;
use App\Models\FlashDeal;
use App\Models\Product;
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
     * Resolve the final price for a variant, applying any active special price,
     * flash deals, and wholesale prices.
     */
    public function resolve(ProductStock $variant, int $quantity = 1): PriceResult
    {
        $basePrice = (float) $variant->price;
        $finalPrice = $basePrice;

        $isFlashDealActive = false;
        $isWholesaleApplied = false;
        $isSpecialActive = false;

        // 1. Check for Wholesale Price (quantity-based override)
        if ($variant->product->wholesale_product) {
            $wholesale = $variant->wholesalePrices()
                ->where('min_qty', '<=', $quantity)
                ->where('max_qty', '>=', $quantity)
                ->first();

            if ($wholesale) {
                $finalPrice = (float) $wholesale->price;
                $isWholesaleApplied = true;
            }
        }

        // 2. Check for Flash Deal (conceptual - logic based on active FlashDeals)
        $flashDeal = $this->getActiveFlashDeal($variant->product);
        if ($flashDeal) {
            // Since we removed 'discount' from products, we assume it's stored in FlashDealProduct
            // for now, or we might need to refactor FlashDealProduct table.
            // For now, let's look for any 'discount' field in the relation.
            $flashDealProduct = $variant->product->flash_deal_products()
                ->where('flash_deal_id', $flashDeal->id)
                ->first();

            if ($flashDealProduct && isset($flashDealProduct->discount)) {
                $finalPrice = $this->applyDiscount(
                    $finalPrice,
                    (float) $flashDealProduct->discount,
                    $flashDealProduct->discount_type ?? 'amount'
                );
                $isFlashDealActive = true;
            }
        }

        // 3. Check for Special Price (Date-based variant discount)
        // If no Flash Deal is active, apply the variant's special price.
        if (! $isFlashDealActive && $this->isSpecialPriceActive($variant)) {
            $finalPrice = $this->calculateSpecialPrice($finalPrice, $variant);
            $isSpecialActive = true;
        }

        $finalPrice = max(0.0, $finalPrice);
        $discountAmount = $basePrice - $finalPrice;

        return new PriceResult(
            basePrice: $basePrice,
            finalPrice: round($finalPrice, 2),
            discountAmount: round($discountAmount, 2),
            isSpecialActive: $isSpecialActive,
            specialPriceType: $isSpecialActive ? $variant->special_price_type : null,
            isFlashDealActive: $isFlashDealActive,
            isWholesaleApplied: $isWholesaleApplied,
        );
    }

    public function isSpecialPriceActive(ProductStock $variant): bool
    {
        return $variant->special_price != null &&
            $variant->special_price_type != null &&
            $this->isWithinDateRange($variant);
    }

    private function getActiveFlashDeal(Product $product): ?FlashDeal
    {
        // Find an active flash deal that contains this product
        return FlashDeal::where('status', 1)
            ->where('start_date', '<=', now()->timestamp) // flash_deal uses timestamps
            ->where('end_date', '>=', now()->timestamp)
            ->whereHas('flash_deal_products', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->first();
    }

    private function isWithinDateRange(ProductStock $variant): bool
    {
        $now = now();

        if ($variant->special_price_start === null || $variant->special_price_end === null) {
            return false;
        }

        return $now->between($variant->special_price_start, $variant->special_price_end);
    }

    private function calculateSpecialPrice(float $currentPrice, ProductStock $variant): float
    {
        $specialValue = (float) $variant->special_price;

        return match ($variant->special_price_type) {
            SpecialPriceType::DiscountPercent => $this->applyDiscount($currentPrice, $specialValue, 'percent'),
            SpecialPriceType::FixedPrice => $specialValue,
            default => $currentPrice,
        };
    }

    private function applyDiscount(float $price, float $discountValue, string $type): float
    {
        if ($type === 'percent' || $type === 'discount_percent') {
            return $price - ($price * $discountValue / 100);
        }

        return $price - $discountValue;
    }
}
