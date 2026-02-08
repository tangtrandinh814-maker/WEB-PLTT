<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\CategoryRepository;
use App\Repositories\ArticleRepository;
use App\Services\ArticleService;
use App\Services\AIClassifierService;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register repositories
        $this->app->singleton(CategoryRepository::class, function ($app) {
            return new CategoryRepository();
        });

        $this->app->singleton(ArticleRepository::class, function ($app) {
            return new ArticleRepository();
        });

        // Register services
        $this->app->singleton(ArticleService::class, function ($app) {
            return new ArticleService();
        });

        $this->app->singleton(AIClassifierService::class, function ($app) {
            return new AIClassifierService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default string length for migrations
        Schema::defaultStringLength(191);
    }
}
