<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateBrandingSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('branding.header_logo', '');
        $this->migrator->add('branding.footer_logo', '');
        $this->migrator->add('branding.site_icon', '');
        $this->migrator->add('branding.system_logo_white', '');
        $this->migrator->add('branding.system_logo_black', '');
        $this->migrator->add('branding.admin_login_background', '');
        $this->migrator->add('branding.admin_login_page_image', '');
    }
}
