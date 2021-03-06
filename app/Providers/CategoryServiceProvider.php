<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\Category\CategoryService;
use App\Services\File\FileService;
use Illuminate\Support\ServiceProvider;

class CategoryServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Providers\CategoryServiceProvider', function ($app) {
            return new CategoryService(new Category(), new FileService());
        });
    }
}
