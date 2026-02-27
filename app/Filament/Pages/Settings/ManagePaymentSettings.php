<?php

namespace App\Filament\Pages\Settings;

use App\Filament\Enums\NavigationGroups;
use App\Settings\PaymentSettings;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManagePaymentSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroups::SETTINGS->getLocalizedLabel();
    }

    public static function getNavigationLabel(): string
    {
        return __('admin/settings.payment.title');
    }

    public function getTitle(): string
    {
        return __('admin/settings.payment.title');
    }

    protected static string $settings = PaymentSettings::class;

    protected static ?int $navigationSort = 4;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('admin/settings.payment.sections.gateways'))
                    ->schema([
                        Toggle::make('cash_payment')->label(__('admin/settings.payment.fields.cash_payment')),
                        Toggle::make('payumoney_payment')->label(__('admin/settings.payment.fields.payumoney_payment')),
                    ])->columns(2),

                Section::make(__('admin/settings.payment.sections.sandbox_mode'))
                    ->schema([
                        Toggle::make('paypal_sandbox')->label(__('admin/settings.payment.fields.paypal_sandbox')),
                        Toggle::make('sslcommerz_sandbox')->label(__('admin/settings.payment.fields.sslcommerz_sandbox')),
                        Toggle::make('instamojo_sandbox')->label(__('admin/settings.payment.fields.instamojo_sandbox')),
                        Toggle::make('bkash_sandbox')->label(__('admin/settings.payment.fields.bkash_sandbox')),
                        Toggle::make('nagad_sandbox')->label(__('admin/settings.payment.fields.nagad_sandbox')),
                        Toggle::make('aamarpay_sandbox')->label(__('admin/settings.payment.fields.aamarpay_sandbox')),
                        Toggle::make('iyzico_sandbox')->label(__('admin/settings.payment.fields.iyzico_sandbox')),
                        Toggle::make('voguepay_sandbox')->label(__('admin/settings.payment.fields.voguepay_sandbox')),
                        Toggle::make('authorizenet_sandbox')->label(__('admin/settings.payment.fields.authorizenet_sandbox')),
                        Toggle::make('payhere_sandbox')->label(__('admin/settings.payment.fields.payhere_sandbox')),
                        Toggle::make('proxypay_sandbox')->label(__('admin/settings.payment.fields.proxypay_sandbox')),
                    ])->columns(3),
            ]);
    }
}
