<?php

namespace App\Providers;

use App\Services\MusicApiService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MusicApiService::class, function ($app) {
            return new MusicApiService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::component('components.header', 'header');
        Blade::component('components.footer', 'footer');
    }
}