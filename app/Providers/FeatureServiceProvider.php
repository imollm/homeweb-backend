<?php

namespace App\Providers;

use App\Services\Feature\FeatureService;
use Illuminate\Support\ServiceProvider;

class FeatureServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Providers\FeatureServiceProvider', function ($app) {
            return new FeatureService();
        });
    }
}
