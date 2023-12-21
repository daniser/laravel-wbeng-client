<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class WBEngineServiceProvider extends ServiceProvider //implements DeferrableProvider
{
    /**
     * All of the container singletons that should be registered.
     *
     * @var array<string, string>
     */
    public array $singletons = [
        'wbeng-client' => ConnectionManager::class,
        'wbeng-store' => StorageManager::class,
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerRouteBindingCallback();
        $this->registerRoutes();
        $this->registerResources();
        $this->registerCommands();

        if ($this->app->runningInConsole()) {
            $this->offerPublishing();
            $this->registerMigrations();
        }
    }

    /**
     * Register the WBEngine Client implicit session resolution callback.
     */
    protected function registerRouteBindingCallback(): void
    {
        Route::substituteImplicitBindingsUsing(Support\SessionRouteBinding::resolveForRoute(...));
    }

    /**
     * Register the WBEngine Client routes.
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
     * Register the WBEngine Client Artisan commands.
     */
    protected function registerCommands(): void
    {
        $this->commands([
            Console\SearchCommand::class,
            Console\SelectCommand::class,
        ]);
    }

    /**
     * Setup the resource publishing groups for WBEngine Client.
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
        $this->registerServices();
    }

    /**
     * Setup the configuration for WBEngine Client.
     */
    protected function configure(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/wbeng-api.php', 'wbeng-api');
        $this->mergeConfigFrom(__DIR__.'/../config/wbeng-client.php', 'wbeng-client');
    }

    /**
     * Register WBEngine Client's services in the container.
     */
    protected function registerServices(): void
    {
        /** @phpstan-ignore-next-line */
        $this->app->bind(StateInterface::class, $this->app['config']['wbeng-client.state']);

        $this->app->singleton('wbeng-serializer', static function ($app) {
            return SerializerFactory::createSerializer($app['config']['wbeng-client.serializer']);
        });
        $this->app->alias('wbeng-serializer', SerializerInterface::class);

        $this->app->singleton('wbeng-client.connection', static fn ($app) => $app['wbeng-client']->connection());
        $this->app->alias('wbeng-client', Contracts\ClientFactory::class);
        $this->app->alias('wbeng-client.connection', ClientInterface::class);

        $this->app->extend(Client::class, static function (Client $client, Container $container) {
            /** @var list<class-string> $middleware */
            $middleware = config('wbeng-client.middleware', []);

            return new ExtendedClient($client, new Pipeline($container), $middleware);
        });

        $this->app->singleton('wbeng-store.store', static fn ($app) => $app['wbeng-store']->connection());
        $this->app->alias('wbeng-store', Contracts\StorageFactory::class);
        $this->app->alias('wbeng-store.store', Contracts\SessionFactory::class);
        $this->app->alias('wbeng-store.store', Contracts\StateStorage::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return list<string>
     */
    public function provides(): array
    {
        return [
            StateInterface::class,
            'wbeng-serializer', SerializerInterface::class,
            'wbeng-client', 'wbeng-client.connection',
            Contracts\ClientFactory::class, ClientInterface::class,
            'wbeng-store', 'wbeng-store.store',
            Contracts\StorageFactory::class, Contracts\SessionFactory::class, Contracts\StateStorage::class,
        ];
    }
}
