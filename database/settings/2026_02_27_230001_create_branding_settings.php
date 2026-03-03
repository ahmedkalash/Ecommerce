<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateBrandingSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('branding.header_logo', '');
        $this->migrator->add('branding.footer_logo', '');
        $this->migrator->add('branding.site_icon', '');
    }
}
