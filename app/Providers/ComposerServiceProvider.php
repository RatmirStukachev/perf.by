<?php

namespace App\Providers;

use App\ViewComposers\CallBackFormComposer;
use App\ViewComposers\CartDeliveryComposer;
use App\ViewComposers\ContactsComposer;
use App\ViewComposers\MenuCategoriesComposer;
use App\ViewComposers\MenuComposer;
use App\ViewComposers\MetricsComposer;
use App\ViewComposers\ProductBlockComposer;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        view()->composer(
            [
                'general.header',
                'general.menus.mobile-menu',
                'general.footer',
            ],
            MenuComposer::class,
        );

        view()->composer(
            [
                'general.header',
                'general.menus.mobile-menu',
                'general.footer',
            ],
            MenuCategoriesComposer::class,
        );

        view()->composer(
            ['*'], MetricsComposer::class,
        );

        view()->composer(
            ['*'], ContactsComposer::class,
        );

        view()->composer(
            ['product', 'cart'], ProductBlockComposer::class,
        );

        view()->composer(
            ['cart'], CartDeliveryComposer::class,
        );

        view()->composer(
            ['*'], CallBackFormComposer::class,
        );
    }
}
