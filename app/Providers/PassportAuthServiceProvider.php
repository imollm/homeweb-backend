<?php

namespace App\Providers;

use App\Services\PassportAuthService;
use Illuminate\Support\ServiceProvider;

class PassportAuthServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Providers\PassportAuthServiceProvider', function ($app) {
            return new PassportAuthService();
        });
    }
}
