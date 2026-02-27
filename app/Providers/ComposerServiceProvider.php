<?php

namespace App\Providers;

use App\Http\ViewComposers\CartComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // View::composer(
        //     ['frontend.inc.footer', 'frontend.'.get_setting('homepage_select').'.partials.cart', 'frontend.'.get_setting('homepage_select').'.partials.product_box_1'],
        //     CartComposer::class
        // );
    }
}
