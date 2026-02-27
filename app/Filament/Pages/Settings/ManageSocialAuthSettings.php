<?php

namespace App\Filament\Pages\Settings;

use App\Enums\SocialProvider;
use App\Filament\Enums\NavigationGroups;
use App\Settings\SocialAuthSettings;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageSocialAuthSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-share';

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroups::SETTINGS->getLocalizedLabel();
    }

    public static function getNavigationLabel(): string
    {
        return __('admin/settings.social_login.title');
    }

    public function getTitle(): string
    {
        return __('admin/settings.social_login.title');
    }

    protected static string $settings = SocialAuthSettings::class;

    protected static ?int $navigationSort = 3;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('admin/settings.social_login.sections.features'))
                    ->schema([
                        Toggle::make(SocialProvider::FACEBOOK->value.'_login')
                            ->label(__('admin/settings.social_login.fields.facebook_login')),
                        Toggle::make(SocialProvider::GOOGLE->value.'_login')
                            ->label(__('admin/settings.social_login.fields.google_login')),
                        Toggle::make(SocialProvider::TWITTER->value.'_login')
                            ->label(__('admin/settings.social_login.fields.twitter_login')),
                    ])->columns(3),
            ]);
    }
}
