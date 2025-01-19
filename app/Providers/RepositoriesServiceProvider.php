<?php

namespace App\Providers;

use App\Http\Repositories\Settings\FeaturedCourseRepository;
use App\Http\Repositories\Settings\FeaturedCourseRepositoryImpl;
use App\Http\Repositories\Settings\HomeBannerRepository;
use App\Http\Repositories\Settings\HomeBannerRepositoryImpl;
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

        /* Settings */
        $this->app->bind(PageBannerRepository::class, PageBannerRepositoryImpl::class);
        $this->app->bind(HomeBannerRepository::class, HomeBannerRepositoryImpl::class);
        $this->app->bind(FeaturedCourseRepository::class, FeaturedCourseRepositoryImpl::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
