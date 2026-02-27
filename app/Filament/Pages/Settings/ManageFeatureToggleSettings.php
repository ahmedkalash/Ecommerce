<?php

namespace App\Filament\Pages\Settings;

use App\Filament\Enums\NavigationGroups;
use App\Settings\FeatureToggleSettings;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageFeatureToggleSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroups::SETTINGS->getLocalizedLabel();
    }

    public static function getNavigationLabel(): string
    {
        return __('admin/settings.feature_toggles.title');
    }

    public function getTitle(): string
    {
        return __('admin/settings.feature_toggles.title');
    }

    protected static string $settings = FeatureToggleSettings::class;

    protected static ?int $navigationSort = 7;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('admin/settings.feature_toggles.sections.core_features'))
                    ->schema([
                        Toggle::make('email_verification')->label(__('admin/settings.feature_toggles.fields.email_verification')),
                        Toggle::make('wallet_system')->label(__('admin/settings.feature_toggles.fields.wallet_system')),
                        Toggle::make('coupon_system')->label(__('admin/settings.feature_toggles.fields.coupon_system')),
                        Toggle::make('conversation_system')->label(__('admin/settings.feature_toggles.fields.conversation_system')),
                        Toggle::make('vendor_system_activation')->label(__('admin/settings.feature_toggles.fields.vendor_system_activation')),
                        Toggle::make('classified_product')->label(__('admin/settings.feature_toggles.fields.classified_product')),
                        Toggle::make('pickup_point')->label(__('admin/settings.feature_toggles.fields.pickup_point')),
                        Toggle::make('guest_checkout_active')->label(__('admin/settings.feature_toggles.fields.guest_checkout_active')),
                        Toggle::make('product_activation')->label(__('admin/settings.feature_toggles.fields.product_activation')),
                        Toggle::make('last_viewed_product_activation')->label(__('admin/settings.feature_toggles.fields.last_viewed_product_activation')),
                        Toggle::make('show_vendors')->label(__('admin/settings.feature_toggles.fields.show_vendors')),
                        Toggle::make('show_language_switcher')->label(__('admin/settings.feature_toggles.fields.show_language_switcher')),
                        Toggle::make('show_currency_switcher')->label(__('admin/settings.feature_toggles.fields.show_currency_switcher')),
                    ])->columns(3),

                Section::make(__('admin/settings.feature_toggles.sections.vendor_system'))
                    ->schema([
                        Toggle::make('min_order_amount_check_activat')
                            ->label(__('admin/settings.feature_toggles.fields.min_order_amount_check_activat')),
                        TextInput::make('minimum_order_amount')
                            ->numeric()
                            ->label(__('admin/settings.feature_toggles.fields.minimum_order_amount')),
                        TextInput::make('vendor_commission')
                            ->numeric()
                            ->label(__('admin/settings.feature_toggles.fields.vendor_commission')),
                        Select::make('seller_commission_type')
                            ->options([
                                'amount' => __('admin/settings.general.options.amount_symbol'),
                                // Assuming standard amounts usually
                                'percent' => '%',
                            ])
                            ->label(__('admin/settings.feature_toggles.fields.seller_commission_type')),
                        Toggle::make('category_wise_commission')
                            ->label(__('admin/settings.feature_toggles.fields.category_wise_commission')),
                    ])->columns(2),

                Section::make(__('admin/settings.feature_toggles.sections.ui_preferences'))
                    ->schema([
                        Select::make('notification_show_type')
                            ->options([
                                'toastr' => 'Toastr',
                                'sweetalert' => 'Sweet Alert',
                            ])->label(__('admin/settings.feature_toggles.fields.notification_show_type')),
                    ])->columns(2),
            ]);
    }
}
