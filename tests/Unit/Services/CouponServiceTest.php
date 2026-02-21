<?php

namespace Tests\Unit\Services;

use App\DTOs\CouponDTO;
use App\Enums\Coupons\CouponDiscountTypes;
use App\Enums\Coupons\CouponTypes;
use App\Models\Coupon;
use App\Services\CouponService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CouponServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected CouponService $couponService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->couponService = app(CouponService::class);
    }

    public function test_can_create_cart_based_coupon()
    {
        $data = [
            'type' => CouponTypes::CART_BASED->value,
            'label' => 'Summer Sale',
            'code' => 'SUMMER2026',
            'discount' => 10.00,
            'discount_type' => CouponDiscountTypes::PERCENTAGE->value,
            'min_money_spent' => 100.00,
            'max_discount' => 50.00,
            'usage_limit' => 100,
            'product_ids' => [],
            'start_date' => Carbon::now()->toDateTimeString(),
            'end_date' => Carbon::now()->addDays(7)->toDateTimeString(),
        ];

        $dto = CouponDTO::fromArray($data);
        $coupon = $this->couponService->create($dto);

        $this->assertInstanceOf(Coupon::class, $coupon);
        $this->assertEquals('Summer Sale', $coupon->label);
        $this->assertEquals('SUMMER2026', $coupon->code);
        $this->assertEquals(10.00, $coupon->discount);
    }

    public function test_can_create_product_based_coupon()
    {
        $data = [
            'type' => CouponTypes::PRODUCT_BASED->value,
            'label' => 'Shoe Sale',
            'code' => 'SHOES20',
            'discount' => 20.00,
            'discount_type' => CouponDiscountTypes::FIXED->value,
            'product_ids' => [1, 2, 3],
            'start_date' => Carbon::now()->toDateTimeString(),
            'end_date' => Carbon::now()->addDays(7)->toDateTimeString(),
        ];

        $dto = CouponDTO::fromArray($data);
        $coupon = $this->couponService->create($dto);

        $this->assertEquals(CouponTypes::PRODUCT_BASED, $coupon->type);
        $this->assertEquals([1, 2, 3], $coupon->product_ids);
    }

    public function test_cannot_create_coupon_with_invalid_date_range()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('End date cannot be before start date.');

        $data = [
            'type' => CouponTypes::CART_BASED->value,
            'label' => 'Invalid Date',
            'code' => 'INVALID',
            'discount' => 10.00,
            'discount_type' => CouponDiscountTypes::PERCENTAGE->value,
            'start_date' => Carbon::now()->addDays(1)->toDateTimeString(),
            'end_date' => Carbon::now()->toDateTimeString(),
        ];

        $dto = CouponDTO::fromArray($data);
        $this->couponService->create($dto);
    }

    public function test_can_update_coupon()
    {
        $coupon = Coupon::create([
            'type' => CouponTypes::CART_BASED->value,
            'label' => 'Old Label',
            'code' => 'OLDCODE',
            'discount' => 5.00,
            'discount_type' => CouponDiscountTypes::FIXED->value,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays(1),
        ]);

        $data = [
            'type' => CouponTypes::CART_BASED->value,
            'label' => 'New Label',
            'code' => 'OLDCODE',
            'discount' => 15.00,
            'discount_type' => CouponDiscountTypes::FIXED->value,
            'start_date' => Carbon::now()->toDateTimeString(),
            'end_date' => Carbon::now()->addDays(2)->toDateTimeString(),
        ];

        $dto = CouponDTO::fromArray($data);
        $updatedCoupon = $this->couponService->update($coupon, $dto);

        $this->assertEquals('New Label', $updatedCoupon->label);
        $this->assertEquals(15.00, $updatedCoupon->discount);
    }

    public function test_can_delete_coupon()
    {
        $coupon = Coupon::create([
            'type' => CouponTypes::CART_BASED->value,
            'label' => 'To Delete',
            'code' => 'DELETE',
            'discount' => 5.00,
            'discount_type' => CouponDiscountTypes::FIXED->value,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays(1),
        ]);

        $this->couponService->delete($coupon);

        $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
    }

    public function test_validate_coupon_throws_exception_if_inactive()
    {
        $coupon = Coupon::factory()->create([
            'start_date' => Carbon::now()->addDays(1),
            'end_date' => Carbon::now()->addDays(5),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('This coupon is currently inactive.');

        $this->couponService->validateCoupon($coupon, 100);
    }

    public function test_validate_coupon_throws_exception_if_minimum_spend_not_met()
    {
        $coupon = Coupon::factory()->create([
            'start_date' => Carbon::now()->subDays(1),
            'end_date' => Carbon::now()->addDays(5),
            'min_money_spent' => 50.00,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('You must spend at least '.$coupon->min_money_spent.' to use this coupon.');

        $this->couponService->validateCoupon($coupon, 40.00);
    }

    public function test_calculate_discount_for_cart_based_percentage()
    {
        $coupon = Coupon::factory()->create([
            'type' => CouponTypes::CART_BASED,
            'discount' => 10.00,
            'discount_type' => CouponDiscountTypes::PERCENTAGE,
            'start_date' => Carbon::now()->subDays(1),
            'end_date' => Carbon::now()->addDays(5),
            'max_discount' => 15.00,
            'min_money_spent' => null,
            'usage_limit' => null,
        ]);

        // 10% of 100 is 10
        $this->assertEquals(10.00, $this->couponService->calculateDiscount($coupon, 100.00));

        // 10% of 200 is 20, but max discount is 15
        $this->assertEquals(15.00, $this->couponService->calculateDiscount($coupon, 200.00));
    }

    public function test_calculate_discount_for_product_based_fixed()
    {
        $coupon = Coupon::factory()->create([
            'type' => CouponTypes::PRODUCT_BASED,
            'product_ids' => [1, 2],
            'discount' => 5.00,
            'discount_type' => CouponDiscountTypes::FIXED,
            'start_date' => Carbon::now()->subDays(1),
            'end_date' => Carbon::now()->addDays(5),
            'min_money_spent' => null,
            'usage_limit' => null,
        ]);

        $cartItems = [
            ['product_id' => 1, 'price' => 10.00, 'quantity' => 1], // Qualifying
            ['product_id' => 3, 'price' => 20.00, 'quantity' => 1], // Non-qualifying
        ];

        // Based directly on configured discount as long as there is an applicable total.
        // Discount is fixed 5. Applied to product 1 value (10.00). Total discount = 5.00.
        $this->assertEquals(5.00, $this->couponService->calculateDiscount($coupon, 30.00, $cartItems));
    }

    public function test_apply_coupon_records_usage_and_increments_count()
    {
        $coupon = Coupon::factory()->create([
            'type' => CouponTypes::CART_BASED,
            'discount' => 10.00,
            'discount_type' => CouponDiscountTypes::FIXED,
            'start_date' => Carbon::now()->subDays(1),
            'end_date' => Carbon::now()->addDays(5),
            'used_count' => 0,
            'usage_limit' => 5,
            'min_money_spent' => null,
        ]);

        // Create an order mock id directly in DB to avoid needing a factory
        $orderId = \Illuminate\Support\Facades\DB::table('orders')->insertGetId([
            'user_id' => 1,
            'guest_id' => null,
            'seller_id' => null,
            'shipping_address' => '{}',
            'delivery_status' => 'pending',
            'payment_type' => 'cash_on_delivery',
            'payment_status' => 'unpaid',
            'payment_details' => null,
            'grand_total' => 100.00,
            'coupon_discount' => 0.00,
            'code' => 'TESTCODE123',
            'date' => now()->timestamp,
            'viewed' => 0,
            'delivery_viewed' => 0,
            'payment_status_viewed' => 0,
            'commission_calculated' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->couponService->applyCoupon(
            $coupon,
            100.00, // cart total
            1, // user id mock
            $orderId // order id mock
        );

        $coupon->refresh();

        $this->assertEquals(1, $coupon->used_count);
        $this->assertDatabaseHas('coupon_usages', [
            'coupon_id' => $coupon->id,
            'user_id' => 1,
            'order_id' => $orderId,
            'discount_amount' => 10.00,
        ]);
    }
}
