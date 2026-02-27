<?php

namespace App\Filament\Pages\Settings;

use App\Enums\ShippingType;
use App\Filament\Enums\NavigationGroups;
use App\Settings\ShippingSettings;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageShippingSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroups::SETTINGS->getLocalizedLabel();
    }

    public static function getNavigationLabel(): string
    {
        return __('admin/settings.shipping.title');
    }

    public function getTitle(): string
    {
        return __('admin/settings.shipping.title');
    }

    protected static string $settings = ShippingSettings::class;

    protected static ?int $navigationSort = 5;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('admin/settings.shipping.sections.configuration'))
                    ->schema([
                        Select::make('shipping_type')
                            ->label(__('admin/settings.shipping.fields.shipping_type'))
                            ->options([
                                ShippingType::FLAT_RATE->value => __('admin/settings.shipping.options.flat_rate'),
                                'area_wise_shipping' => __('admin/settings.shipping.options.area_wise'),
                                'product_wise_shipping' => __('admin/settings.shipping.options.seller_wise'),
                            ])
                            ->required(),
                        TextInput::make('flat_rate_shipping_cost')
                            ->numeric()
                            ->label(__('admin/settings.shipping.fields.flat_rate_shipping_cost')),
                        TextInput::make('shipping_cost_admin')
                            ->numeric()
                            ->label(__('admin/settings.shipping.fields.shipping_cost_admin')),
                    ])->columns(2),
            ]);
    }
}
