<?php

namespace App\Providers;

use App\Services\PriceHistory\PriceHistoryService;
use Illuminate\Support\ServiceProvider;

class PriceHistoryServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Providers\PriceHistoryServiceProvider', function ($app) {
            return new PriceHistoryService();
        });
    }
}
