<?php

namespace App\Enums;

enum RecaptchaAction: string
{
    case ADMIN_LOGIN = 'recaptcha_admin_login';
    case CUSTOMER_LOGIN = 'recaptcha_customer_login';
    case SELLER_LOGIN = 'recaptcha_seller_login';
    case DELIVERY_BOY_LOGIN = 'recaptcha_delivery_boy_login';
    case CUSTOMER_REGISTER = 'recaptcha_customer_register';
    case SELLER_REGISTER = 'recaptcha_seller_register';
    case FORGOT_PASSWORD = 'recaptcha_forgot_password';
    case CONTACT_US = 'recaptcha_contact_form';
    case CHECKOUT = 'recaptcha_checkout';
    case AFFILIATE_APPLICATION = 'recaptcha_affiliate_apply';

    /**
     * Get the human-readable label or description (optional helper).
     */
    public function label(): string
    {
        return match ($this) {
            self::ADMIN_LOGIN => 'Admin Login',
            self::CUSTOMER_LOGIN => 'Customer Login',
            self::SELLER_LOGIN => 'Seller Login',
            self::DELIVERY_BOY_LOGIN => 'Delivery Boy Login',
            self::CUSTOMER_REGISTER => 'Customer Registration',
            self::SELLER_REGISTER => 'Seller Registration',
            self::FORGOT_PASSWORD => 'Forgot Password',
            self::CONTACT_US => 'Contact Us Form',
            self::CHECKOUT => 'Checkout',
            self::AFFILIATE_APPLICATION => 'Affiliate Application',
        };
    }
}
