<?php

namespace App\Filament\Pages\Settings;

use App\Enums\RecaptchaAction;
use App\Filament\Enums\NavigationGroups;
use App\Settings\ThirdPartyIntegrationSettings;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageThirdPartySettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroups::SETTINGS->getLocalizedLabel();
    }

    public static function getNavigationLabel(): string
    {
        return __('admin/settings.third_party.title');
    }

    public function getTitle(): string
    {
        return __('admin/settings.third_party.title');
    }

    protected static string $settings = ThirdPartyIntegrationSettings::class;

    protected static ?int $navigationSort = 8;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('admin/settings.third_party.sections.google'))
                    ->schema([
                        Toggle::make('google_analytics')->label(__('admin/settings.third_party.fields.google_analytics')),
                        Toggle::make('google_recaptcha')->label(__('admin/settings.third_party.fields.google_recaptcha')),
                        Toggle::make(RecaptchaAction::CUSTOMER_REGISTER->value)->label(__('admin/settings.third_party.fields.recaptcha_customer_register')),
                        Toggle::make('google_map')->label(__('admin/settings.third_party.fields.google_map')),
                        Toggle::make('google_firebase')->label(__('admin/settings.third_party.fields.google_firebase')),
                    ])->columns(2),

                Section::make(__('admin/settings.third_party.sections.facebook'))
                    ->schema([
                        Toggle::make('facebook_pixel')->label(__('admin/settings.third_party.fields.facebook_pixel')),
                        Toggle::make('facebook_chat')->label(__('admin/settings.third_party.fields.facebook_chat')),
                    ])->columns(2),

                Section::make(__('admin/settings.third_party.sections.whatsapp'))
                    ->schema([
                        Toggle::make('whatsapp_chat')->label(__('admin/settings.third_party.fields.whatsapp_chat')),
                        Toggle::make('whatsapp_order')->label(__('admin/settings.third_party.fields.whatsapp_order')),
                        Toggle::make('whatsapp_order_seller_prods')->label(__('admin/settings.third_party.fields.whatsapp_order_seller_prods')),
                    ])->columns(2),

                Section::make(__('admin/settings.third_party.sections.other'))
                    ->schema([
                        Toggle::make('use_floating_buttons')->label(__('admin/settings.third_party.fields.use_floating_buttons')),
                    ])->columns(2),
            ]);
    }
}
