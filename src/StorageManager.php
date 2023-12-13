<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use TTBooking\WBEngine\Contracts\StorableState;

/**
 * @extends Support\Manager<Contracts\StateStorage>
 */
class StorageManager extends Support\Manager implements Contracts\StateStorage, Contracts\StorageFactory
{
    protected string $selectorKey = 'wbeng-client.store';

    public function has(string $id): bool
    {
        return $this->connection()->has($id);
    }

    public function get(string $id): StorableState
    {
        return $this->connection()->get($id);
    }

    public function put(StorableState $state): StorableState
    {
        return $this->connection()->put($state);
    }

    public function session(string $id): Session
    {
        return $this->connection()->session($id);
    }

    /**
     * @param  array{model: class-string<Models\State>}  $config
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createEloquentDriver(array $config): Stores\EloquentStorage
    {
        return $this->container->make(Stores\EloquentStorage::class, $config);
    }

    /**
     * @param  array{table: string}  $config
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createDatabaseDriver(array $config): Stores\DatabaseStorage
    {
        return $this->container->make(Stores\DatabaseStorage::class, $config);
    }

    /**
     * @param  array{path: string}  $config
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createFilesystemDriver(array $config): Stores\FilesystemStorage
    {
        return $this->container->make(Stores\FilesystemStorage::class, $config);
    }

    protected function createArrayDriver(): Stores\ArrayStorage
    {
        return new Stores\ArrayStorage($this->container);
    }

    protected function createNullDriver(): Stores\NullStorage
    {
        return new Stores\NullStorage($this->container);
    }
}
