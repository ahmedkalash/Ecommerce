<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateSeoSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('seo.meta_title', '');
        $this->migrator->add('seo.meta_description', '');
        $this->migrator->add('seo.meta_keywords', '');
        $this->migrator->add('seo.meta_image', '');
    }
}
