<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ShippingSettings extends Settings
{
    public string $shipping_type = '';

    public ?string $flat_rate_shipping_cost = null;

    public ?string $shipping_cost_admin = null;

    public static function group(): string
    {
        return 'shipping';
    }
}
