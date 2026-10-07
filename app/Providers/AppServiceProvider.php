<?php

namespace App\Providers;

use App\Services\NestApiClient;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

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
        $this->preferFileStoresWhenDatabaseTablesMissing();

        View::composer('layouts.app', function ($view) {
            if (! $view->offsetExists('cms')) {
                $view->with('cms', app(\App\Services\CmsService::class)->site());
            }
        });
    }

    private function preferFileStoresWhenDatabaseTablesMissing(): void
    {
        try {
            if (config('session.driver') === 'database' && ! Schema::hasTable((string) config('session.table', 'sessions'))) {
                config(['session.driver' => 'file']);
            }
        } catch (Throwable) {
            config(['session.driver' => 'file']);
        }

        try {
            if (config('cache.default') === 'database' && ! Schema::hasTable('cache')) {
                config(['cache.default' => 'file']);
            }
        } catch (Throwable) {
            config(['cache.default' => 'file']);
        }
    }
}
