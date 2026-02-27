<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateSocialAuthSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('social_auth.facebook_login', 0);
        $this->migrator->add('social_auth.google_login', 0);
        $this->migrator->add('social_auth.twitter_login', 0);
    }
}
