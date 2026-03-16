<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class BrandingSettings extends Settings
{
    public ?string $header_logo = null;

    public ?string $footer_logo = null;

    public ?string $site_icon = null;

    public ?string $system_logo_black = null;

    public static function group(): string
    {
        return 'branding';
    }
}
