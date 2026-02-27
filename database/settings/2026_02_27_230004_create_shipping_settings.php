<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateShippingSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('shipping.shipping_type', 'product_wise_shipping');
        $this->migrator->add('shipping.flat_rate_shipping_cost', '0');
        $this->migrator->add('shipping.shipping_cost_admin', '0');
    }
}
