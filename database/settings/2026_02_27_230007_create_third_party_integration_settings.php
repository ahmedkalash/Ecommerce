<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateThirdPartyIntegrationSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('third_party.google_analytics', 0);
        $this->migrator->add('third_party.google_recaptcha', 0);
        $this->migrator->add('third_party.google_map', 0);
        $this->migrator->add('third_party.google_firebase', 0);
        $this->migrator->add('third_party.facebook_chat', 0);
        $this->migrator->add('third_party.facebook_pixel', 0);
        $this->migrator->add('third_party.whatsapp_chat', 0);
        $this->migrator->add('third_party.whatsapp_order', 0);
        $this->migrator->add('third_party.whatsapp_order_seller_prods', 0);
        $this->migrator->add('third_party.recaptcha_customer_register', 0);
        $this->migrator->add('third_party.use_floating_buttons', 1);
    }
}
