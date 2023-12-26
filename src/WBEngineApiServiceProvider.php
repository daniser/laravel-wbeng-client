<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class WBEngineApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerRouteBindingCallback();
        $this->registerRoutes();
        $this->registerResources();

        if ($this->app->runningInConsole()) {
            $this->offerPublishing();
            $this->registerMigrations();
        }
    }

    /**
     * Register the WBEngine API implicit session resolution callback.
     */
    protected function registerRouteBindingCallback(): void
    {
        Route::substituteImplicitBindingsUsing(Support\SessionRouteBinding::resolveForRoute(...));
    }

    /**
     * Register the WBEngine API routes.
     */
    protected function registerRoutes(): void
    {
        Route::domain($this->app['config']['wbeng-api.domain'] ?? '')
            ->prefix($this->app['config']['wbeng-api.path'] ?? '')
            ->name('wbeng-api.')
            ->namespace('TTBooking\\WBEngine\\Http\\Controllers')
            ->middleware($this->app['config']['wbeng-api.middleware'] ?? 'api')
            ->group(fn () => $this->loadRoutesFrom(__DIR__.'/../routes/api.php'));
    }

    /**
     * Register the WBEngine Client resources.
     */
    protected function registerResources(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'wbeng-client');
    }

    /**
     * Setup the resource publishing groups for WBEngine Client/API.
     */
    protected function offerPublishing(): void
    {
        $this->publishes([
            __DIR__.'/../config/wbeng-api.php' => $this->app->configPath('wbeng-api.php'),
            __DIR__.'/../config/wbeng-client.php' => $this->app->configPath('wbeng-client.php'),
        ], ['wbeng-client-config', 'wbeng-client', 'config']);

        $this->publishes([
            __DIR__.'/../database/migrations' => $this->app->databasePath('migrations'),
        ], ['wbeng-client-migrations', 'wbeng-client', 'migrations']);
    }

    /**
     * Register the WBEngine Client's migrations.
     */
    protected function registerMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->configure();
    }

    /**
     * Setup the configuration for WBEngine API.
     */
    protected function configure(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/wbeng-api.php', 'wbeng-api');
    }
}
