<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateFeatureToggleSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('feature_toggle.email_verification', 0);
        $this->migrator->add('feature_toggle.wallet_system', 0);
        $this->migrator->add('feature_toggle.coupon_system', 0);
        $this->migrator->add('feature_toggle.conversation_system', 1);
        $this->migrator->add('feature_toggle.vendor_system_activation', 1);
        $this->migrator->add('feature_toggle.classified_product', 0);
        $this->migrator->add('feature_toggle.pickup_point', 0);
        $this->migrator->add('feature_toggle.guest_checkout_active', 1);
        $this->migrator->add('feature_toggle.product_activation', 1);
        $this->migrator->add('feature_toggle.last_viewed_product_activation', 1);
        $this->migrator->add('feature_toggle.show_vendors', 1);
        $this->migrator->add('feature_toggle.show_language_switcher', 1);
        $this->migrator->add('feature_toggle.show_currency_switcher', 1);
        $this->migrator->add('feature_toggle.min_order_amount_check_activat', 0);
        $this->migrator->add('feature_toggle.minimum_order_amount', '0');
        $this->migrator->add('feature_toggle.vendor_commission', '20');
        $this->migrator->add('feature_toggle.seller_commission_type', 'fixed_rate');
        $this->migrator->add('feature_toggle.category_wise_commission', 0);
        $this->migrator->add('feature_toggle.notification_show_type', 'only_text');
    }
}
