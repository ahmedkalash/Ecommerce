<?php

namespace App\Services;

use App\DTOs\CouponDTO;
use App\Enums\Coupons\CouponDiscountTypes;
use App\Enums\Coupons\CouponTypes;
use App\Models\Coupon;
use Exception;

class CouponService
{
    /**
     * Create a new coupon.
     *
     * @throws Exception
     */
    public function create(CouponDTO $dto): Coupon
    {
        // Business Logic Validation
        $this->validateCouponData($dto);

        return Coupon::create($dto->toArray());
    }

    /**
     * Update an existing coupon.
     *
     * @throws Exception
     */
    public function update(Coupon $coupon, CouponDTO $dto): Coupon
    {
        // Business Logic Validation
        $this->validateCouponData($dto);

        $coupon->update($dto->toArray());

        return $coupon->fresh();
    }

    /**
     * Delete a coupon.
     */
    public function delete(Coupon $coupon): void
    {
        $coupon->delete();
    }

    /**
     * Validate coupon data based on business rules.
     *
     * @throws Exception
     */
    protected function validateCouponData(CouponDTO $dto): void
    {
        $startDate = $dto->start_date;
        $endDate = $dto->end_date;

        if ($endDate->lessThan($startDate)) {
            throw new Exception('End date cannot be before start date.');
        }

        if ($dto->min_money_spent !== null && $dto->min_money_spent < 0) {
            throw new Exception('Minimum money spent cannot be negative.');
        }

        if ($dto->discount < 0) {
            throw new Exception('Discount cannot be negative.');
        }

        if ($dto->discount_type === CouponDiscountTypes::PERCENTAGE && $dto->discount > 100) {
            throw new Exception('Percentage discount cannot vary more than 100%.');
        }

        if ($dto->type === CouponTypes::PRODUCT_BASED && empty($dto->product_ids)) {
            throw new Exception('Product based coupons must have at least one product selected.');
        }
    }

    /**
     * Validate if a coupon is applicable to a given cart total and items.
     * $cartItems is expected to be an array of arrays or objects containing 'product_id' and 'price'/'quantity'.
     *
     * @throws Exception
     */
    public function validateCoupon(Coupon $coupon, float $cartTotal, array $cartItems = []): void
    {
        // 1. Check active status manually (in case they didn't use the scope)
        $now = now();
        if ($now->lessThan($coupon->start_date) || $now->greaterThan($coupon->end_date)) {
            throw new Exception('This coupon is currently inactive.');
        }

        // 2. Check usage limits
        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            throw new Exception('This coupon has reached its usage limit.');
        }

        // 3. Check minimum spend
        if ($coupon->min_money_spent !== null && $cartTotal < $coupon->min_money_spent) {
            throw new Exception("You must spend at least $coupon->min_money_spent to use this coupon.");
        }

        // 4. If product-based, ensure at least one qualifying product is in the cart
        if ($coupon->type === CouponTypes::PRODUCT_BASED) {
            if (empty($coupon->product_ids)) {
                throw new Exception('This product-based coupon has no configured products.');
            }

            $hasQualifyingProduct = false;
            foreach ($cartItems as $item) {
                $productId = is_object($item) ? $item->product_id : ($item['product_id'] ?? null);
                if ($productId && in_array($productId, $coupon->product_ids)) {
                    $hasQualifyingProduct = true;
                    break;
                }
            }

            if (! $hasQualifyingProduct) {
                throw new Exception('This coupon does not apply to any products in your cart.');
            }
        }
    }

    /**
     * Calculate the discount amount for a given cart.
     */
    public function calculateDiscount(Coupon $coupon, float $cartTotal, array $cartItems = []): float
    {
        try {
            // First, validate it's even applicable
            $this->validateCoupon($coupon, $cartTotal, $cartItems);
        } catch (Exception $e) {
            // If invalid, discount is 0
            return 0.0;
        }

        $discountValue = 0.0;

        if ($coupon->type === CouponTypes::CART_BASED) {
            $baseAmount = $cartTotal;
        } else {
            // PRODUCT_BASED: Only discount the qualifying products' totals
            $baseAmount = 0.0;
            foreach ($cartItems as $item) {
                $productId = is_object($item) ? $item->product_id : ($item['product_id'] ?? null);
                if ($productId && in_array($productId, $coupon->product_ids)) {
                    $price = is_object($item) ? $item->price : ($item['price'] ?? 0);
                    $quantity = is_object($item) ? $item->quantity : ($item['quantity'] ?? 1);
                    $baseAmount += ($price * $quantity);
                }
            }
        }

        // Calculate based on type
        if ($coupon->discount_type === CouponDiscountTypes::FIXED) {
            $discountValue = $coupon->discount;
            // Fixed discount can't exceed the applicable base amount
            $discountValue = min($discountValue, $baseAmount);
        } else {
            // PERCENTAGE
            $discountValue = $baseAmount * ($coupon->discount / 100);
        }

        // Apply maximum discount cap if set
        if ($coupon->max_discount !== null && $discountValue > $coupon->max_discount) {
            $discountValue = $coupon->max_discount;
        }

        return round($discountValue, 2);
    }

    /**
     * Apply a coupon to an order, incrementing its usage count and creating an audit record.
     * Note: This strictly records the usage, it assumes you've already calculated and applied
     * the discount to the actual order total.
     *
     * @throws Exception
     */
    public function applyCoupon(
        Coupon $coupon,
        float $cartTotal,
        int $userId,
        int $orderId,
        array $cartItems = []
    ): void {
        // Re-validate right before applying to be safe against race conditions
        $this->validateCoupon($coupon, $cartTotal, $cartItems);

        // Calculate final discount applied (for the audit trail)
        $discountAmount = $this->calculateDiscount($coupon, $cartTotal, $cartItems);

        \Illuminate\Support\Facades\DB::transaction(function () use ($coupon, $userId, $orderId, $discountAmount) {
            // Lock the row to prevent race conditions on usage limit
            $couponModel = Coupon::where('id', $coupon->id)->lockForUpdate()->firstOrFail();

            if ($couponModel->usage_limit !== null && $couponModel->used_count >= $couponModel->usage_limit) {
                throw new Exception('Coupon usage limit reached during checkout.');
            }

            // Record the usage
            $couponModel->couponUsages()->create([
                'user_id' => $userId,
                'order_id' => $orderId,
                'discount_amount' => $discountAmount,
            ]);

            // Increment count
            $couponModel->increment('used_count');
        });
    }
}
