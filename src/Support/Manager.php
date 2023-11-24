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
    /** Connection selector key. */
    protected string $selectorKey;

    /** Connection pool key. */
    protected string $poolKey;

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
        /** @var string */
        return $this->config->get($this->getSelectorKey(), 'default');
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
     * Get the connection selector key.
     */
    protected function getSelectorKey(): string
    {
        return $this->selectorKey;
    }

    /**
     * Get the connection pool key.
     */
    protected function getPoolKey(): string
    {
        return $this->poolKey ??= Str::plural($this->getSelectorKey());
    }

    /**
     * Get the connection configuration.
     *
     * @return array{driver: string}
     */
    protected function getConfig(string $name): array
    {
        $poolKey = $this->getPoolKey();

        /** @var array{driver: string} */
        return $this->config->get("$poolKey.$name", []) + ['driver' => $name];
    }
}
