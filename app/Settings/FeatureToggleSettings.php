<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class FeatureToggleSettings extends Settings
{
    public ?int $email_verification = 0;

    public ?int $wallet_system = 0;

    public ?int $coupon_system = 0;

    public ?int $conversation_system = 0;

    public ?int $vendor_system_activation = 0;

    public ?int $classified_product = 0;

    public ?int $pickup_point = 0;

    public ?int $guest_checkout_active = 0;

    public ?int $product_activation = 0;

    public ?int $last_viewed_product_activation = 0;

    public ?int $show_vendors = 0;

    public ?int $show_language_switcher = 0;

    public ?int $show_currency_switcher = 0;

    public ?int $min_order_amount_check_activat = 0;

    public ?string $minimum_order_amount = null;

    public ?string $vendor_commission = null;

    public ?string $seller_commission_type = null;

    public ?int $category_wise_commission = 0;

    public ?string $notification_show_type = null;

    public static function group(): string
    {
        return 'feature_toggle';
    }
}
