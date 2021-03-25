<?php

namespace App\Providers;

use App\Models\Property;
use App\Models\RangePrice;
use App\Models\User;
use App\Services\Property\PropertyService;
use Illuminate\Support\ServiceProvider;

class PropertyServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Providers\PropertyServiceProvider', function ($app) {
            return new PropertyService(new Property(), new User(), new RangePrice());
        });
    }
}
