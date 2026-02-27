<?php

namespace App\Filament\Pages\Settings;

use App\Filament\Enums\NavigationGroups;
use App\Models\Currency;
use App\Settings\GeneralSettings;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageGeneralSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroups::SETTINGS->getLocalizedLabel();
    }

    public static function getNavigationLabel(): string
    {
        return __('admin/settings.general.title');
    }

    public function getTitle(): string
    {
        return __('admin/settings.general.title');
    }

    protected static string $settings = GeneralSettings::class;

    protected static ?int $navigationSort = 1;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('admin/settings.general.sections.general'))
                    ->schema([
                        TextInput::make('site_name')
                            ->label(__('admin/settings.general.fields.site_name'))
                            ->required(),
                        TextInput::make('site_motto')
                            ->label(__('admin/settings.general.fields.site_motto'))
                            ->required(),
                        TextInput::make('timezone')
                            ->label(__('admin/settings.general.fields.timezone'))
                            ->required(),
                        TextInput::make('current_version')
                            ->label(__('admin/settings.general.fields.current_version'))
                            ->required(),
                    ])->columns(2),

                Section::make(__('admin/settings.general.sections.currency_formatting'))
                    ->schema([
                        Select::make('system_default_currency')
                            ->label(__('admin/settings.general.fields.system_default_currency'))
                            ->options(function () {
                                return class_exists(Currency::class) ? Currency::where('status', 1)->pluck('name',
                                    'id') : [];
                            })
                            ->searchable(),
                        Select::make('home_default_currency')
                            ->label(__('admin/settings.general.fields.home_default_currency'))
                            ->options(function () {
                                return class_exists(Currency::class) ? Currency::where('status', 1)->pluck('name',
                                    'id') : [];
                            })
                            ->searchable(),
                        Select::make('currency_format')
                            ->label(__('admin/settings.general.fields.currency_format'))
                            ->options([
                                '1' => __('admin/settings.general.options.symbol_amount'),
                                '2' => __('admin/settings.general.options.amount_symbol'),
                            ]),
                        Select::make('symbol_format')
                            ->label(__('admin/settings.general.fields.symbol_format'))
                            ->options([
                                '1' => __('admin/settings.general.options.without_space'),
                                '2' => __('admin/settings.general.options.with_space'),
                            ]),
                        TextInput::make('no_of_decimals')
                            ->label(__('admin/settings.general.fields.no_of_decimals'))
                            ->numeric(),
                        Select::make('decimal_separator')
                            ->label(__('admin/settings.general.fields.decimal_separator'))
                            ->options([
                                '1' => __('admin/settings.general.options.dot'),
                                '2' => __('admin/settings.general.options.comma'),
                            ]),
                    ])->columns(2),

                Section::make(__('admin/settings.general.sections.system_variables'))
                    ->schema([
                        Toggle::make('maintenance_mode')
                            ->label(__('admin/settings.general.fields.maintenance_mode')),
                        Select::make('uploaded_image_format')
                            ->label(__('admin/settings.general.fields.uploaded_image_format'))
                            ->options([
                                'webp' => 'WEBP',
                                'jpg' => 'JPG',
                                'png' => 'PNG',
                            ]),
                    ])->columns(2),
            ]);
    }
}
