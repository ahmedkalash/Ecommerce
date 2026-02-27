<?php

namespace App\Providers\Filament;

use App\Filament\Enums\NavigationGroups;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\SpatieLaravelTranslatablePlugin;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Kenepa\TranslationManager\TranslationManagerPlugin;

class AdminPanelProvider extends PanelProvider
{
    /**
     * @throws \Exception
     */
    public function panel(Panel $panel): Panel
    {
        $supportedLocales = collect(config('app.supported_locales', []))
            ->pluck('language')
            ->unique()
            ->values()
            ->toArray() ?: ['en'];

        return $panel
            ->default()
            ->id('admin')
            ->path('adminf')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\AppLocale::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->loginRouteSlug('login')
            ->homeUrl('adminf')
            ->authGuard('admin')
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                NavigationGroup::make(NavigationGroups::CATALOG->value)
                    ->label(fn () => NavigationGroups::CATALOG->getLocalizedLabel()),
                NavigationGroup::make(NavigationGroups::SHOP_MANAGEMENT->value)
                    ->label(fn () => NavigationGroups::SHOP_MANAGEMENT->getLocalizedLabel()),
                NavigationGroup::make(NavigationGroups::USER_MANAGEMENT->value)
                    ->label(fn () => NavigationGroups::USER_MANAGEMENT->getLocalizedLabel()),
                NavigationGroup::make(NavigationGroups::SETTINGS->value)
                    ->label(fn () => NavigationGroups::SETTINGS->getLocalizedLabel()),
            ])
            ->plugins([
                TranslationManagerPlugin::make(),
                SpatieLaravelTranslatablePlugin::make()
                    ->defaultLocales($supportedLocales),
            ]);
    }
}
