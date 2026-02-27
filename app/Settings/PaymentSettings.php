<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PaymentSettings extends Settings
{
    public ?int $cash_payment = 0;

    public ?int $payumoney_payment = 0;

    public ?int $paypal_sandbox = 0;

    public ?int $sslcommerz_sandbox = 0;

    public ?int $instamojo_sandbox = 0;

    public ?int $bkash_sandbox = 0;

    public ?int $nagad_sandbox = 0;

    public ?int $aamarpay_sandbox = 0;

    public ?int $iyzico_sandbox = 0;

    public ?int $voguepay_sandbox = 0;

    public ?int $authorizenet_sandbox = 0;

    public ?int $payhere_sandbox = 0;

    public ?int $proxypay_sandbox = 0;

    public static function group(): string
    {
        return 'payment';
    }
}
