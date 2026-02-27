<?php

return [
    'group' => 'Settings',

    'general' => [
        'title' => 'General Settings',
        'sections' => [
            'general' => 'General',
            'currency_formatting' => 'Currency & Formatting',
            'system_variables' => 'System Variables',
        ],
        'fields' => [
            'site_name' => 'Site Name',
            'site_motto' => 'Site Motto',
            'timezone' => 'Timezone',
            'current_version' => 'Current Version',
            'system_default_currency' => 'System Default Currency',
            'home_default_currency' => 'Home Default Currency',
            'currency_format' => 'Currency Format',
            'symbol_format' => 'Symbol Format',
            'no_of_decimals' => 'Number of Decimals',
            'decimal_separator' => 'Decimal Separator',
            'maintenance_mode' => 'Maintenance Mode',
            'uploaded_image_format' => 'Uploaded Image Format',
        ],
        'options' => [
            'symbol_amount' => 'Symbol Amount (Ex: $100)',
            'amount_symbol' => 'Amount Symbol (Ex: 100$)',
            'without_space' => 'Without Space (Ex: $100)',
            'with_space' => 'With Space (Ex: $ 100)',
            'dot' => 'Dot (.)',
            'comma' => 'Comma (,)',
        ],
    ],

    'branding' => [
        'title' => 'Branding Settings',
        'sections' => [
            'logos_icons' => 'Logos & Icons',
            'login_page' => 'Admin Login Page',
        ],
        'fields' => [
            'header_logo' => 'Header Logo',
            'footer_logo' => 'Footer Logo',
            'site_icon' => 'Site Icon',
            'system_logo_white' => 'System Logo (White)',
            'system_logo_black' => 'System Logo (Black)',
            'admin_login_background' => 'Admin Login Background Image',
            'admin_login_page_image' => 'Admin Login Page Side Image',
        ],
    ],

    'social_login' => [
        'title' => 'Social Auth Settings',
        'sections' => [
            'features' => 'Social Login Features',
        ],
        'fields' => [
            'facebook_login' => 'Facebook Login',
            'google_login' => 'Google Login',
            'twitter_login' => 'Twitter Login',
        ],
    ],

    'payment' => [
        'title' => 'Payment Settings',
        'sections' => [
            'gateways' => 'Payment Gateways',
            'sandbox_mode' => 'Sandbox Modes (Testing)',
        ],
        'fields' => [
            'cash_payment' => 'Cash on Delivery',
            'payumoney_payment' => 'PayUMoney Payment',
            'paypal_sandbox' => 'PayPal Sandbox',
            'sslcommerz_sandbox' => 'SSLCommerz Sandbox',
            'instamojo_sandbox' => 'Instamojo Sandbox',
            'bkash_sandbox' => 'bKash Sandbox',
            'nagad_sandbox' => 'Nagad Sandbox',
            'aamarpay_sandbox' => 'Aamarpay Sandbox',
            'iyzico_sandbox' => 'Iyzico Sandbox',
            'voguepay_sandbox' => 'VoguePay Sandbox',
            'authorizenet_sandbox' => 'Authorize.Net Sandbox',
            'payhere_sandbox' => 'PayHere Sandbox',
            'proxypay_sandbox' => 'ProxyPay Sandbox',
        ],
    ],

    'shipping' => [
        'title' => 'Shipping Settings',
        'sections' => [
            'configuration' => 'Shipping Configuration',
        ],
        'fields' => [
            'shipping_type' => 'Shipping Type',
            'flat_rate_shipping_cost' => 'Flat Rate Shipping Cost',
            'shipping_cost_admin' => 'Admin Shipping Cost',
        ],
        'options' => [
            'flat_rate' => 'Flat Rate',
            'seller_wise' => 'Seller Wise',
            'area_wise' => 'Area Wise',
        ],
    ],

    'seo' => [
        'title' => 'SEO Settings',
        'sections' => [
            'metadata' => 'SEO Metadata',
        ],
        'fields' => [
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Description',
            'meta_keywords' => 'Meta Keywords',
            'meta_image' => 'Meta Image',
        ],
    ],

    'feature_toggles' => [
        'title' => 'Feature Toggles',
        'sections' => [
            'core_features' => 'Core Systems',
            'vendor_system' => 'Vendor System',
            'ui_preferences' => 'UI Preferences',
        ],
        'fields' => [
            'email_verification' => 'Email Verification',
            'wallet_system' => 'Wallet System',
            'coupon_system' => 'Coupon System',
            'conversation_system' => 'Conversation System',
            'vendor_system_activation' => 'Vendor System Activation',
            'classified_product' => 'Classified Products',
            'pickup_point' => 'Pickup Points',
            'guest_checkout_active' => 'Guest Checkout',
            'product_activation' => 'Product Activation',
            'last_viewed_product_activation' => 'Last Viewed Product History',
            'show_vendors' => 'Show Vendors on Map/List',
            'show_language_switcher' => 'Show Language Switcher',
            'show_currency_switcher' => 'Show Currency Switcher',
            'min_order_amount_check_activat' => 'Minimum Order Amount Check',
            'minimum_order_amount' => 'Minimum Order Amount',
            'vendor_commission' => 'Vendor Commission',
            'seller_commission_type' => 'Seller Commission Type',
            'category_wise_commission' => 'Category Wise Commission',
            'notification_show_type' => 'Notification Show Type',
        ],
    ],

    'third_party' => [
        'title' => 'Third-Party Integrations',
        'sections' => [
            'google' => 'Google Integrations',
            'facebook' => 'Facebook Integrations',
            'whatsapp' => 'WhatsApp Integrations',
            'other' => 'Other UI Preferences',
        ],
        'fields' => [
            'google_analytics' => 'Google Analytics',
            'google_recaptcha' => 'Google reCAPTCHA',
            'google_map' => 'Google Maps',
            'google_firebase' => 'Google Firebase (Push Notifications)',
            'facebook_chat' => 'Facebook Chat',
            'facebook_pixel' => 'Facebook Pixel',
            'whatsapp_chat' => 'WhatsApp Chat',
            'whatsapp_order' => 'WhatsApp Order Button',
            'whatsapp_order_seller_prods' => 'WhatsApp Order for Seller Products',
            'recaptcha_customer_register' => 'reCAPTCHA for Customer Registration',
            'use_floating_buttons' => 'Use Floating Buttons',
        ],
    ],
];
