<?php

namespace App\Providers;

use App\Http\Repositories\Settings\PageBannerRepository;
use App\Http\Repositories\Settings\PageBannerRepositoryImpl;
use App\Http\Repositories\SystemLogs\SystemLogRepository;
use App\Http\Repositories\SystemLogs\SystemLogRepositoryImpl;
use Illuminate\Support\ServiceProvider;

class RepositoriesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        $this->app->bind(SystemLogRepository::class, SystemLogRepositoryImpl::class);
        $this->app->bind(PageBannerRepository::class, PageBannerRepositoryImpl::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
