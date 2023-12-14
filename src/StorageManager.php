<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use Illuminate\Support\Enumerable;
use TTBooking\WBEngine\Contracts\SessionFactory;
use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\Contracts\StorableState;
use TTBooking\WBEngine\Contracts\StorageFactory;

/**
 * @extends Support\Manager<ExtendedStorage>
 *
 * @implements StorageFactory<ExtendedStorage>
 */
class StorageManager extends Support\Manager implements SessionFactory, StateStorage, StorageFactory
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

    public function where(array $conditions): Enumerable
    {
        return $this->connection()->where($conditions);
    }

    public function all(): Enumerable
    {
        return $this->connection()->all();
    }

    public function session(string $id, ?string $connection = null): Client
    {
        return $this->connection()->session($id, $connection);
    }

    /**
     * @param  array{model: class-string<Models\State>}  $config
     * @return ExtendedStorage<Stores\EloquentStorage>
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createEloquentDriver(array $config): ExtendedStorage
    {
        /** @var ExtendedStorage<Stores\EloquentStorage> */
        return $this->container->make(Stores\EloquentStorage::class, $config);
    }

    /**
     * @param  array{table: string}  $config
     * @return ExtendedStorage<Stores\DatabaseStorage>
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createDatabaseDriver(array $config): ExtendedStorage
    {
        /** @var ExtendedStorage<Stores\DatabaseStorage> */
        return $this->container->make(Stores\DatabaseStorage::class, $config);
    }

    /**
     * @param  array{path: string}  $config
     * @return ExtendedStorage<Stores\FilesystemStorage>
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createFilesystemDriver(array $config): ExtendedStorage
    {
        /** @var ExtendedStorage<Stores\FilesystemStorage> */
        return $this->container->make(Stores\FilesystemStorage::class, $config);
    }

    /**
     * @return ExtendedStorage<Stores\ArrayStorage>
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createArrayDriver(): ExtendedStorage
    {
        /** @var ExtendedStorage<Stores\ArrayStorage> */
        return $this->container->make(Stores\ArrayStorage::class);
    }

    /**
     * @return ExtendedStorage<Stores\NullStorage>
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createNullDriver(): ExtendedStorage
    {
        /** @var ExtendedStorage<Stores\NullStorage> */
        return $this->container->make(Stores\NullStorage::class);
    }
}
