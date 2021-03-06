<?php

namespace App\Providers;

use App\Services\Property\PropertyService;
use App\Services\Tour\TourService;
use App\Services\User\UserService;
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
            return new TourService(new PropertyService(), new UserService());
        });
    }
}
