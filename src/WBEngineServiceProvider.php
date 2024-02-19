<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use Illuminate\Console\Command;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use TTBooking\WBEngine\Contracts\Prompter;
use TTBooking\WBEngine\Http\Controllers\AutocompleteController;
use TTBooking\WBEngine\Middleware\AmendMiddleware;

class WBEngineServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * All of the container singletons that should be registered.
     *
     * @var array<string, class-string>
     */
    public array $singletons = [
        'wbeng-client' => ConnectionManager::class,
        'wbeng-store' => StorageManager::class,
    ];

    /**
     * The commands to be registered.
     *
     * @var list<class-string<Command>>
     */
    protected array $commands = [
        Console\SearchCommand::class,
        Console\SelectCommand::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->configure();
        $this->registerServices();
        $this->registerCommands();
    }

    /**
     * Setup the configuration for WBEngine Client.
     */
    protected function configure(): void
    {
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

            /** @phpstan-ignore-next-line */
            return new ExtendedClient($client, new Pipeline($container), $middleware);
        });

        $this->app->when(AmendMiddleware::class)->needs('$typeAmenders')->giveConfig('wbeng-client.amenders.type', []);
        $this->app->when(AmendMiddleware::class)->needs('$pathAmenders')->giveConfig('wbeng-client.amenders.path', []);

        $this->app->singleton('wbeng-client.prompters.airport', fn () => $this->prompter('airport'));
        $this->app->singleton('wbeng-client.prompters.airline', fn () => $this->prompter('airline'));

        $this->app->singleton('wbeng-store.store', static fn ($app) => $app['wbeng-store']->connection());
        $this->app->alias('wbeng-store', Contracts\StorageFactory::class);
        $this->app->alias('wbeng-store.store', Contracts\SessionFactory::class);
        $this->app->alias('wbeng-store.store', Contracts\StateStorage::class);
    }

    private function prompter(string $type): Prompter
    {
        $prompter = config("wbeng-client.prompters.$type");

        if (! $prompter) {
            return new class implements Prompter
            {
                public function prompt(string $input): array
                {
                    return [];
                }
            };
        }

        if (! is_string($prompter) || ! is_subclass_of($prompter, Prompter::class)) {
            throw new InvalidArgumentException('Prompter must be class implementing ['.Prompter::class.'] interface.');
        }

        return $this->app->make($prompter);
    }

    /**
     * Register the WBEngine Client Artisan commands.
     */
    protected function registerCommands(): void
    {
        foreach ($this->commands as $command) {
            $this->app->singleton($command);
        }

        $this->commands($this->commands);
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
            'wbeng-client.prompters.airport', 'wbeng-client.prompters.airline',
            ...$this->commands,
        ];
    }
}
