<?php

namespace App\Providers;

use App\Services\CountryService;
use Illuminate\Support\ServiceProvider;

class CountryServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Providers\CountryServiceProvider', function ($app) {
            return new CountryService();
        });
    }
}
