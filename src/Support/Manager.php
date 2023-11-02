<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Support;

use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Str;
use InvalidArgumentException;
use TTBooking\WBEngine\Contracts\Factory;

/**
 * @template TConnection of object
 *
 * @implements Factory<TConnection>
 */
abstract class Manager implements Factory
{
    /** Configuration name. */
    protected string $configName;

    /**
     * The registered custom driver creators.
     *
     * @var array<string, Closure>
     */
    protected array $customCreators = [];

    /**
     * The array of created connections.
     *
     * @var array<string, TConnection>
     */
    protected array $connections = [];

    /**
     * Create a new manager instance.
     *
     * @param  Container  $container  The container instance.
     * @param  Repository  $config    The configuration repository instance.
     */
    public function __construct(protected Container $container, protected Repository $config)
    {
    }

    /**
     * Get the default driver name.
     */
    public function getDefaultDriver(): string
    {
        $configName = $this->getConfigName();

        /** @var string */
        return $this->config->get("$configName.default", 'default');
    }

    public function connection(string $name = null): object
    {
        $name ??= $this->getDefaultDriver();

        return $this->connections[$name] ??= $this->resolve($name);
    }

    public function getConnections(): array
    {
        return $this->connections;
    }

    /**
     * Dynamically call the default connection instance.
     *
     * @param  array<mixed>  $parameters
     */
    public function __call(string $method, array $parameters): mixed
    {
        return $this->connection()->$method(...$parameters);
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @return $this
     */
    public function extend(string $driver, Closure $callback): static
    {
        $this->customCreators[$driver] = $callback->bindTo($this, $this) ?? $callback;

        return $this;
    }

    /**
     * Resolve the given connection.
     *
     * @phpstan-return TConnection
     *
     * @throws InvalidArgumentException
     */
    protected function resolve(string $name): object
    {
        $config = $this->getConfig($name);

        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($config);
        } else {
            $method = 'create'.Str::studly($config['driver']).'Driver';

            if (method_exists($this, $method)) {
                return $this->$method($config, $name);
            }
        }

        throw new InvalidArgumentException("Driver [{$config['driver']}] not supported.");
    }

    /**
     * Call a custom driver creator.
     *
     * @param  array{driver: string}  $config
     * @phpstan-return TConnection
     */
    protected function callCustomCreator(array $config): object
    {
        return $this->customCreators[$config['driver']]($this->container, $config);
    }

    /**
     * Get the configuration name.
     */
    protected function getConfigName(): string
    {
        return $this->configName;
    }

    /**
     * Get the cache connection configuration.
     *
     * @return array{driver: string}
     */
    protected function getConfig(string $name): array
    {
        $configName = $this->getConfigName();

        /** @var array{driver: string} */
        return $this->config->get("$configName.connections.$name", []) + ['driver' => $name];
    }
}
