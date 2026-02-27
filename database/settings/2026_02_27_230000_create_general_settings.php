<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateGeneralSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', '');
        $this->migrator->add('general.site_motto', '');
        $this->migrator->add('general.timezone', config('app.timezone', 'UTC'));
        $this->migrator->add('general.system_default_currency', '1');
        $this->migrator->add('general.home_default_currency', '1');
        $this->migrator->add('general.currency_format', '1');
        $this->migrator->add('general.symbol_format', '1');
        $this->migrator->add('general.no_of_decimals', '2');
        $this->migrator->add('general.decimal_separator', '1');
        $this->migrator->add('general.maintenance_mode', 0);
        $this->migrator->add('general.current_version', '9.9.9');
        $this->migrator->add('general.uploaded_image_format', 'default');
    }
}
