<?php

namespace Database\Seeders;

use App\Enums\Coupons\CouponDiscountTypes;
use App\Enums\Coupons\CouponTypes;
use App\Models\Coupon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CouponDemoSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME50',
                'label' => ['en' => 'Welcome Discount', 'ar' => 'خصم الترحيب'],
                'type' => CouponTypes::PRODUCT_BASED->value,
                'discount' => 50,
                'discount_type' => CouponDiscountTypes::FIXED->value,
                'start_date' => Carbon::now()->subDay(),
                'end_date' => Carbon::now()->addYear(),
            ],
            [
                'code' => 'SUMMER20',
                'label' => ['en' => 'Summer Sale', 'ar' => 'تخفيضات الصيف'],
                'type' => CouponTypes::CART_BASED->value,
                'discount' => 20,
                'discount_type' => CouponDiscountTypes::PERCENTAGE->value,
                'start_date' => Carbon::now()->subDay(),
                'end_date' => Carbon::now()->addMonths(3),
            ],
        ];

        foreach ($coupons as $data) {
            Coupon::firstOrCreate(
                ['code' => $data['code']],
                $data
            );
        }
    }
}
