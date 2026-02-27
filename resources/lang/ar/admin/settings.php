<?php

return [
    'group' => 'الإعدادات',

    'general' => [
        'title' => 'الإعدادات العامة',
        'sections' => [
            'general' => 'عام',
            'currency_formatting' => 'العملة والتنسيق',
            'system_variables' => 'متغيرات النظام',
        ],
        'fields' => [
            'site_name' => 'اسم الموقع',
            'site_motto' => 'شعار الموقع',
            'timezone' => 'المنطقة الزمنية',
            'current_version' => 'الإصدار الحالي',
            'system_default_currency' => 'العملة الافتراضية للنظام',
            'home_default_currency' => 'العملة الافتراضية للرئيسية',
            'currency_format' => 'تنسيق العملة',
            'symbol_format' => 'تنسيق الرمز',
            'no_of_decimals' => 'عدد الكسور العشرية',
            'decimal_separator' => 'الفاصل العشري',
            'maintenance_mode' => 'وضع الصيانة',
            'uploaded_image_format' => 'تنسيق الصور المرفوعة',
        ],
        'options' => [
            'symbol_amount' => 'الرمز والمبلغ (مثال: $100)',
            'amount_symbol' => 'المبلغ والرمز (مثال: 100$)',
            'without_space' => 'بدون مسافة (مثال: $100)',
            'with_space' => 'بمسافة (مثال: $ 100)',
            'dot' => 'نقطة (.)',
            'comma' => 'فاصلة (,)',
        ],
    ],

    'branding' => [
        'title' => 'إعدادات العلامة التجارية',
        'sections' => [
            'logos_icons' => 'الشعارات والأيقونات',
            'login_page' => 'صفحة تسجيل دخول الإدارة',
        ],
        'fields' => [
            'header_logo' => 'شعار الرأس',
            'footer_logo' => 'شعار التذييل',
            'site_icon' => 'أيقونة الموقع',
            'system_logo_white' => 'شعار النظام (أبيض)',
            'system_logo_black' => 'شعار النظام (أسود)',
            'admin_login_background' => 'صورة خلفية تسجيل دخول الإدارة',
            'admin_login_page_image' => 'الصورة الجانبية لتسجيل دخول الإدارة',
        ],
    ],

    'social_login' => [
        'title' => 'إعدادات تسجيل الدخول الاجتماعي',
        'sections' => [
            'features' => 'ميزات تسجيل الدخول',
        ],
        'fields' => [
            'facebook_login' => 'تسجيل الدخول عبر فيسبوك',
            'google_login' => 'تسجيل الدخول عبر جوجل',
            'twitter_login' => 'تسجيل الدخول عبر تويتر',
        ],
    ],

    'payment' => [
        'title' => 'إعدادات الدفع',
        'sections' => [
            'gateways' => 'بوابات الدفع',
            'sandbox_mode' => 'أوضاع الاختبار (Sandbox)',
        ],
        'fields' => [
            'cash_payment' => 'الدفع عند الاستلام',
            'payumoney_payment' => 'دفع PayUMoney',
            'paypal_sandbox' => 'PayPal (وضع الاختبار)',
            'sslcommerz_sandbox' => 'SSLCommerz (وضع الاختبار)',
            'instamojo_sandbox' => 'Instamojo (وضع الاختبار)',
            'bkash_sandbox' => 'bKash (وضع الاختبار)',
            'nagad_sandbox' => 'Nagad (وضع الاختبار)',
            'aamarpay_sandbox' => 'Aamarpay (وضع الاختبار)',
            'iyzico_sandbox' => 'Iyzico (وضع الاختبار)',
            'voguepay_sandbox' => 'VoguePay (وضع الاختبار)',
            'authorizenet_sandbox' => 'Authorize.Net (وضع الاختبار)',
            'payhere_sandbox' => 'PayHere (وضع الاختبار)',
            'proxypay_sandbox' => 'ProxyPay (وضع الاختبار)',
        ],
    ],

    'shipping' => [
        'title' => 'إعدادات الشحن',
        'sections' => [
            'configuration' => 'إعدادات الشحن',
        ],
        'fields' => [
            'shipping_type' => 'نوع الشحن',
            'flat_rate_shipping_cost' => 'تكلفة الشحن الموحد',
            'shipping_cost_admin' => 'تكلفة الشحن للإدارة',
        ],
        'options' => [
            'flat_rate' => 'شحن موحد',
            'seller_wise' => 'حسب البائع',
            'area_wise' => 'حسب المنطقة',
        ],
    ],

    'seo' => [
        'title' => 'إعدادات محركات البحث (SEO)',
        'sections' => [
            'metadata' => 'بيانات SEO الوصفية',
        ],
        'fields' => [
            'meta_title' => 'العنوان الوصفي',
            'meta_description' => 'الوصف الوصفي',
            'meta_keywords' => 'الكلمات الدلالية',
            'meta_image' => 'الصورة الوصفية',
        ],
    ],

    'feature_toggles' => [
        'title' => 'تفعيل الميزات',
        'sections' => [
            'core_features' => 'الأنظمة الأساسية',
            'vendor_system' => 'نظام البائعين',
            'ui_preferences' => 'تفضيلات الواجهة',
        ],
        'fields' => [
            'email_verification' => 'التحقق من البريد الإلكتروني',
            'wallet_system' => 'نظام المحفظة',
            'coupon_system' => 'نظام القسائم الشرائية',
            'conversation_system' => 'نظام المحادثات',
            'vendor_system_activation' => 'تفعيل نظام البائعين',
            'classified_product' => 'المنتجات المبوبة',
            'pickup_point' => 'نقاط الاستلام',
            'guest_checkout_active' => 'الدفع كضيف',
            'product_activation' => 'تفعيل المنتجات',
            'last_viewed_product_activation' => 'سجل المنتجات المعروضة مؤخرا',
            'show_vendors' => 'إظهار البائعين في الخريطة/القائمة',
            'show_language_switcher' => 'إظهار مبدل اللغات',
            'show_currency_switcher' => 'إظهار مبدل العملات',
            'min_order_amount_check_activat' => 'التحقق من الحد الأدنى للطلب',
            'minimum_order_amount' => 'الحد الأدنى لقيمة الطلب',
            'vendor_commission' => 'عمولة البائع',
            'seller_commission_type' => 'نوع عمولة البائع',
            'category_wise_commission' => 'عمولة حسب الفئة',
            'notification_show_type' => 'طريقة عرض الإشعارات',
        ],
    ],

    'third_party' => [
        'title' => 'إعدادات الطرف الثالث',
        'sections' => [
            'google' => 'إضافات جوجل',
            'facebook' => 'إضافات فيسبوك',
            'whatsapp' => 'إضافات واتساب',
            'other' => 'تفضيلات واجهة أخرى',
        ],
        'fields' => [
            'google_analytics' => 'إحصاءات جوجل',
            'google_recaptcha' => 'Google reCAPTCHA',
            'google_map' => 'خرائط جوجل',
            'google_firebase' => 'Google Firebase (إشعارات الويب)',
            'facebook_chat' => 'محادثات فيسبوك',
            'facebook_pixel' => 'فيسبوك بيكسل',
            'whatsapp_chat' => 'محادثة واتساب',
            'whatsapp_order' => 'زر الطلب عبر الواتساب',
            'whatsapp_order_seller_prods' => 'الطلب عبر الواتساب لمنتجات البائع',
            'recaptcha_customer_register' => 'reCAPTCHA في تسجيل المستهلكين',
            'use_floating_buttons' => 'استخدام الأزرار العائمة',
        ],
    ],
];
