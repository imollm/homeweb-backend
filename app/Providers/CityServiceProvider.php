<?php

namespace App\Providers;

use App\Services\City\CityService;
use Illuminate\Support\ServiceProvider;

class CityServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Providers\CityServiceProvider', function ($app) {
            return new CityService();
        });
    }
}
