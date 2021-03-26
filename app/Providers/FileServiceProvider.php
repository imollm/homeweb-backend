<?php

namespace App\Providers;

use App\Services\File\FileService;
use Illuminate\Support\ServiceProvider;

class FileServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Providers\FileServiceProvider', function ($app) {
            return new FileService();
        });
    }
}
