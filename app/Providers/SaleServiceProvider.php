<?php

namespace App\Providers;

use App\Services\Sale\SaleService;
use Illuminate\Support\ServiceProvider;

class SaleServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Providers\SaleServiceProvider', function ($app) {
            return new SaleService();
        });
    }
}
