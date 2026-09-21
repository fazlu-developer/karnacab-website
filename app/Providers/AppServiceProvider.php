<?php

namespace App\Providers;

use App\Services\NestApiClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(NestApiClient::class, fn () => NestApiClient::make());
        $this->app->singleton(\App\Services\LaravelApiClient::class, fn () => \App\Services\LaravelApiClient::make());
        $this->app->singleton(\App\Services\TripFareService::class);
    }

    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            if (! $view->offsetExists('cms')) {
                $view->with('cms', app(\App\Services\CmsService::class)->site());
            }
        });
    }
}
