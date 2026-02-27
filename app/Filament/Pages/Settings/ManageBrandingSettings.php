<?php

namespace App\Filament\Pages\Settings;

use App\Filament\Enums\NavigationGroups;
use App\Settings\BrandingSettings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageBrandingSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroups::SETTINGS->getLocalizedLabel();
    }

    public static function getNavigationLabel(): string
    {
        return __('admin/settings.branding.title');
    }

    public function getTitle(): string
    {
        return __('admin/settings.branding.title');
    }

    protected static string $settings = BrandingSettings::class;

    protected static ?int $navigationSort = 2;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('admin/settings.branding.sections.logos_icons'))
                    ->schema([
                        FileUpload::make('header_logo')
                            ->label(__('admin/settings.branding.fields.header_logo'))
                            ->image(),
                        FileUpload::make('footer_logo')
                            ->label(__('admin/settings.branding.fields.footer_logo'))
                            ->image(),
                        FileUpload::make('site_icon')
                            ->label(__('admin/settings.branding.fields.site_icon'))
                            ->image(),
                        FileUpload::make('system_logo_white')
                            ->label(__('admin/settings.branding.fields.system_logo_white'))
                            ->image(),
                        FileUpload::make('system_logo_black')
                            ->label(__('admin/settings.branding.fields.system_logo_black'))
                            ->image(),
                    ])->columns(2),

                Section::make(__('admin/settings.branding.sections.login_page'))
                    ->schema([
                        FileUpload::make('admin_login_background')
                            ->label(__('admin/settings.branding.fields.admin_login_background'))
                            ->image(),
                        FileUpload::make('admin_login_page_image')
                            ->label(__('admin/settings.branding.fields.admin_login_page_image'))
                            ->image(),
                    ])->columns(2),
            ]);
    }
}
