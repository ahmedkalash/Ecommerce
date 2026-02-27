<?php

namespace App\Settings;

class SpatieSettingsBridge
{
    protected static array $mapping = [
        // General
        'site_name' => [GeneralSettings::class, 'site_name'],
        'site_motto' => [GeneralSettings::class, 'site_motto'],
        'timezone' => [GeneralSettings::class, 'timezone'],
        'system_default_currency' => [GeneralSettings::class, 'system_default_currency'],
        'home_default_currency' => [GeneralSettings::class, 'home_default_currency'],
        'currency_format' => [GeneralSettings::class, 'currency_format'],
        'symbol_format' => [GeneralSettings::class, 'symbol_format'],
        'no_of_decimals' => [GeneralSettings::class, 'no_of_decimals'],
        'decimal_separator' => [GeneralSettings::class, 'decimal_separator'],
        'maintenance_mode' => [GeneralSettings::class, 'maintenance_mode'],
        'current_version' => [GeneralSettings::class, 'current_version'],
        'uploaded_image_format' => [GeneralSettings::class, 'uploaded_image_format'],

        // Branding
        'header_logo' => [BrandingSettings::class, 'header_logo'],
        'footer_logo' => [BrandingSettings::class, 'footer_logo'],
        'site_icon' => [BrandingSettings::class, 'site_icon'],
        'system_logo_white' => [BrandingSettings::class, 'system_logo_white'],
        'system_logo_black' => [BrandingSettings::class, 'system_logo_black'],
        'admin_login_background' => [BrandingSettings::class, 'admin_login_background'],
        'admin_login_page_image' => [BrandingSettings::class, 'admin_login_page_image'],

        // Social Auth
        'facebook_login' => [SocialAuthSettings::class, 'facebook_login'],
        'google_login' => [SocialAuthSettings::class, 'google_login'],
        'twitter_login' => [SocialAuthSettings::class, 'twitter_login'],

        // Payment
        'cash_payment' => [PaymentSettings::class, 'cash_payment'],
        'payumoney_payment' => [PaymentSettings::class, 'payumoney_payment'],
        'paypal_sandbox' => [PaymentSettings::class, 'paypal_sandbox'],
        'sslcommerz_sandbox' => [PaymentSettings::class, 'sslcommerz_sandbox'],
        'instamojo_sandbox' => [PaymentSettings::class, 'instamojo_sandbox'],
        'bkash_sandbox' => [PaymentSettings::class, 'bkash_sandbox'],
        'nagad_sandbox' => [PaymentSettings::class, 'nagad_sandbox'],
        'aamarpay_sandbox' => [PaymentSettings::class, 'aamarpay_sandbox'],
        'iyzico_sandbox' => [PaymentSettings::class, 'iyzico_sandbox'],
        'voguepay_sandbox' => [PaymentSettings::class, 'voguepay_sandbox'],
        'authorizenet_sandbox' => [PaymentSettings::class, 'authorizenet_sandbox'],
        'payhere_sandbox' => [PaymentSettings::class, 'payhere_sandbox'],
        'proxypay_sandbox' => [PaymentSettings::class, 'proxypay_sandbox'],

        // Shipping
        'shipping_type' => [ShippingSettings::class, 'shipping_type'],
        'flat_rate_shipping_cost' => [ShippingSettings::class, 'flat_rate_shipping_cost'],
        'shipping_cost_admin' => [ShippingSettings::class, 'shipping_cost_admin'],

        // SEO
        'meta_title' => [SeoSettings::class, 'meta_title'],
        'meta_description' => [SeoSettings::class, 'meta_description'],
        'meta_keywords' => [SeoSettings::class, 'meta_keywords'],
        'meta_image' => [SeoSettings::class, 'meta_image'],

        // Feature Toggles
        'email_verification' => [FeatureToggleSettings::class, 'email_verification'],
        'wallet_system' => [FeatureToggleSettings::class, 'wallet_system'],
        'coupon_system' => [FeatureToggleSettings::class, 'coupon_system'],
        'conversation_system' => [FeatureToggleSettings::class, 'conversation_system'],
        'vendor_system_activation' => [FeatureToggleSettings::class, 'vendor_system_activation'],
        'classified_product' => [FeatureToggleSettings::class, 'classified_product'],
        'pickup_point' => [FeatureToggleSettings::class, 'pickup_point'],
        'guest_checkout_active' => [FeatureToggleSettings::class, 'guest_checkout_active'],
        'product_activation' => [FeatureToggleSettings::class, 'product_activation'],
        'last_viewed_product_activation' => [FeatureToggleSettings::class, 'last_viewed_product_activation'],
        'show_vendors' => [FeatureToggleSettings::class, 'show_vendors'],
        'show_language_switcher' => [FeatureToggleSettings::class, 'show_language_switcher'],
        'show_currency_switcher' => [FeatureToggleSettings::class, 'show_currency_switcher'],
        'min_order_amount_check_activat' => [FeatureToggleSettings::class, 'min_order_amount_check_activat'],
        'minimum_order_amount' => [FeatureToggleSettings::class, 'minimum_order_amount'],
        'vendor_commission' => [FeatureToggleSettings::class, 'vendor_commission'],
        'seller_commission_type' => [FeatureToggleSettings::class, 'seller_commission_type'],
        'category_wise_commission' => [FeatureToggleSettings::class, 'category_wise_commission'],
        'notification_show_type' => [FeatureToggleSettings::class, 'notification_show_type'],

        // Third Party
        'google_analytics' => [ThirdPartyIntegrationSettings::class, 'google_analytics'],
        'google_recaptcha' => [ThirdPartyIntegrationSettings::class, 'google_recaptcha'],
        'google_map' => [ThirdPartyIntegrationSettings::class, 'google_map'],
        'google_firebase' => [ThirdPartyIntegrationSettings::class, 'google_firebase'],
        'facebook_chat' => [ThirdPartyIntegrationSettings::class, 'facebook_chat'],
        'facebook_pixel' => [ThirdPartyIntegrationSettings::class, 'facebook_pixel'],
        'whatsapp_chat' => [ThirdPartyIntegrationSettings::class, 'whatsapp_chat'],
        'whatsapp_order' => [ThirdPartyIntegrationSettings::class, 'whatsapp_order'],
        'whatsapp_order_seller_prods' => [ThirdPartyIntegrationSettings::class, 'whatsapp_order_seller_prods'],
        'recaptcha_customer_register' => [ThirdPartyIntegrationSettings::class, 'recaptcha_customer_register'],
        'use_floating_buttons' => [ThirdPartyIntegrationSettings::class, 'use_floating_buttons'],
    ];

    /**
     * Get the mapped setting if it exists, otherwise return null
     */
    public static function get($key)
    {
        if (isset(self::$mapping[$key])) {
            [$class, $property] = self::$mapping[$key];

            return app($class)->{$property};
        }

        return null;
    }
}
