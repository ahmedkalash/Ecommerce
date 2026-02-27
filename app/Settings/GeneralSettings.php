<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name = '';

    public string $site_motto = '';

    public string $timezone = '';

    public ?int $system_default_currency = null;

    public ?int $home_default_currency = null;

    public ?string $currency_format = null;

    public ?string $symbol_format = null;

    public ?string $no_of_decimals = null;

    public string $decimal_separator = '';

    public ?int $maintenance_mode = 0;

    public string $current_version = '';

    public string $uploaded_image_format = '';

    public static function group(): string
    {
        return 'general';
    }
}
