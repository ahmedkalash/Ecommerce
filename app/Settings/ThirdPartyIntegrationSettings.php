<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ThirdPartyIntegrationSettings extends Settings
{
    public ?int $google_analytics = 0;

    public ?int $google_recaptcha = 0;

    public ?int $google_map = 0;

    public ?int $google_firebase = 0;

    public ?int $facebook_chat = 0;

    public ?int $facebook_pixel = 0;

    public ?int $whatsapp_chat = 0;

    public ?int $whatsapp_order = 0;

    public ?int $whatsapp_order_seller_prods = 0;

    public ?int $recaptcha_customer_register = 0;

    public ?int $use_floating_buttons = 0;

    public static function group(): string
    {
        return 'third_party';
    }
}
