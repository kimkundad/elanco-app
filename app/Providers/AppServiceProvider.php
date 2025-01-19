<?php

namespace App\Providers;

use App\Providers\Image\ImageUploadServiceProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

//Import Schema
use Illuminate\Support\Facades\URL;

// Import URL Facade

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        if (config('app.env') !== 'local') { // ใช้เฉพาะ production หรือ staging
            URL::forceScheme('https');
        }
        Schema::defaultStringLength(191);

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('ImageUploadService', function ($app) {
            return new ImageUploadServiceProvider();
        });
    }
}
