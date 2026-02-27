<?php

namespace App\Filament\Pages\Settings;

use App\Filament\Enums\NavigationGroups;
use App\Settings\SeoSettings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageSeoSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass-circle';

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroups::SETTINGS->getLocalizedLabel();
    }

    public static function getNavigationLabel(): string
    {
        return __('admin/settings.seo.title');
    }

    public function getTitle(): string
    {
        return __('admin/settings.seo.title');
    }

    protected static string $settings = SeoSettings::class;

    protected static ?int $navigationSort = 6;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('admin/settings.seo.sections.metadata'))
                    ->schema([
                        TextInput::make('meta_title')
                            ->label(__('admin/settings.seo.fields.meta_title'))
                            ->maxLength(255),
                        Textarea::make('meta_keywords')
                            ->label(__('admin/settings.seo.fields.meta_keywords'))
                            ->helperText('Separate keywords with commas'),
                        Textarea::make('meta_description')
                            ->label(__('admin/settings.seo.fields.meta_description'))
                            ->rows(3)
                            ->columnSpanFull(),
                        FileUpload::make('meta_image')
                            ->label(__('admin/settings.seo.fields.meta_image'))
                            ->image()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
