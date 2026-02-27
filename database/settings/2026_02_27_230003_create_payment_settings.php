<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreatePaymentSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('payment.cash_payment', 1);
        $this->migrator->add('payment.payumoney_payment', 1);
        $this->migrator->add('payment.paypal_sandbox', 0);
        $this->migrator->add('payment.sslcommerz_sandbox', 1);
        $this->migrator->add('payment.instamojo_sandbox', 1);
        $this->migrator->add('payment.bkash_sandbox', 1);
        $this->migrator->add('payment.nagad_sandbox', 0);
        $this->migrator->add('payment.aamarpay_sandbox', 0);
        $this->migrator->add('payment.iyzico_sandbox', 1);
        $this->migrator->add('payment.voguepay_sandbox', 0);
        $this->migrator->add('payment.authorizenet_sandbox', 1);
        $this->migrator->add('payment.payhere_sandbox', 0);
        $this->migrator->add('payment.proxypay_sandbox', 1);
    }
}
