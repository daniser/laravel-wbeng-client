<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class WBEngineServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->commands([
            Console\SearchCommand::class,
        ]);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/wbeng-client.php' => $this->app->configPath('wbeng-client.php'),
            ], ['wbeng-client-config', 'wbeng-client', 'config']);
        }
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/wbeng-client.php', 'wbeng-client');

        $this->app->singleton('wbeng-client', static fn ($app) => new ConnectionManager($app));
        $this->app->singleton('wbeng-client.connection', static fn ($app) => $app['wbeng-client']->connection());
        $this->app->bind(ClientInterface::class, 'wbeng-client.connection');
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return ['wbeng-client', 'wbeng-client.connection', ClientInterface::class];
    }
}
