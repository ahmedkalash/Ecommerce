<?php

namespace App\Providers;

use App\Settings\BrandingSettings;
use Filament\Forms\Components\Select;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(255);
        Paginator::useBootstrap();

        Select::configureUsing(function (Select $select) {
            $select->native(false);
        });

        $this->shareGlobalViewsVariables();
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function shareGlobalViewsVariables(): void
    {
        $header_logo = app(BrandingSettings::class)->header_logo;
        $footer_logo = app(BrandingSettings::class)->footer_logo;
        $site_icon = app(BrandingSettings::class)->site_icon;

        View::share('header_logo', $header_logo);
        View::share('footer_logo', $footer_logo);
        View::share('site_icon', $site_icon);
    }
}
