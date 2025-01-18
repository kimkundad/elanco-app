<?php

namespace App\Providers;

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
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
