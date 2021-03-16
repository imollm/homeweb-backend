<?php

namespace App\Providers;

use App\Services\Tour\TourService;
use Illuminate\Support\ServiceProvider;

class TourServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Providers\TourServiceProvider', function ($app) {
            return new TourService();
        });
    }
}
