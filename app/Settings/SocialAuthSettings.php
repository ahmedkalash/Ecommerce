<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SocialAuthSettings extends Settings
{
    public ?int $facebook_login = 0;

    public ?int $google_login = 0;

    public ?int $twitter_login = 0;

    public static function group(): string
    {
        return 'social_auth';
    }
}
