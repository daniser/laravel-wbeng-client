<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\ServiceProvider;
use TTBooking\WBEngine\Contracts\ClientFactory;
use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\Contracts\StorageFactory;

class WBEngineServiceProvider extends ServiceProvider implements DeferrableProvider
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
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'wbeng-client');

        $this->commands([
            Console\SearchCommand::class,
        ]);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/wbeng-client.php' => $this->app->configPath('wbeng-client.php'),
            ], ['wbeng-client-config', 'wbeng-client', 'config']);

            $this->publishes([
                __DIR__.'/../database/migrations' => $this->app->databasePath('migrations'),
            ], ['wbeng-client-migrations', 'wbeng-client', 'migrations']);

            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/wbeng-client.php', 'wbeng-client');

        /** @phpstan-ignore-next-line */
        $this->app->bind(StateInterface::class, $this->app['config']['wbeng-client.state']);

        $this->app->singleton('wbeng-serializer', static function ($app) {
            return SerializerFactory::createSerializer($app['config']['wbeng-client.serializer']);
        });
        $this->app->alias('wbeng-serializer', SerializerInterface::class);

        $this->app->singleton('wbeng-client.connection', static fn ($app) => $app['wbeng-client']->connection());
        $this->app->alias('wbeng-client', ClientFactory::class);
        $this->app->alias('wbeng-client.connection', ClientInterface::class);
        $this->app->alias('wbeng-client.connection', AsyncClientInterface::class);

        $this->app->extend(Client::class, static function (Client $client, Container $container) {
            /** @var list<class-string> $middleware */
            $middleware = config('wbeng-client.middleware', []);

            return new ExtendedClient($client, new Pipeline($container), $middleware);
        });

        $this->app->singleton('wbeng-store.store', static fn ($app) => $app['wbeng-store']->connection());
        $this->app->alias('wbeng-store', StorageFactory::class);
        $this->app->alias('wbeng-store.store', StateStorage::class);

        $this->app->extend(StateStorage::class, static function (StateStorage $storage, Container $container) {
            return $container->make(ExtendedStorage::class, compact('storage'));
        });
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
            ClientFactory::class, ClientInterface::class, AsyncClientInterface::class,
            'wbeng-store', 'wbeng-store.store',
            StorageFactory::class, StateStorage::class,
        ];
    }
}
