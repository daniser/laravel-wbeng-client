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
     * @param  array{stores: list<string|null>}  $config
     * @return ExtendedStorage<Stores\AggregateStorage>
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createAggregateDriver(array $config): ExtendedStorage
    {
        return $this->createDriver(Stores\AggregateStorage::class, $config);
    }

    /**
     * @param  array{model: class-string<Models\State>}  $config
     * @return ExtendedStorage<Stores\EloquentStorage>
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createEloquentDriver(array $config): ExtendedStorage
    {
        return $this->createDriver(Stores\EloquentStorage::class, $config);
    }

    /**
     * @param  array{table: string}  $config
     * @return ExtendedStorage<Stores\DatabaseStorage>
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createDatabaseDriver(array $config): ExtendedStorage
    {
        return $this->createDriver(Stores\DatabaseStorage::class, $config);
    }

    /**
     * @param  array{path: string}  $config
     * @return ExtendedStorage<Stores\FilesystemStorage>
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createFilesystemDriver(array $config): ExtendedStorage
    {
        return $this->createDriver(Stores\FilesystemStorage::class, $config);
    }

    /**
     * @return ExtendedStorage<Stores\ArrayStorage>
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createArrayDriver(): ExtendedStorage
    {
        return $this->createDriver(Stores\ArrayStorage::class);
    }

    /**
     * @return ExtendedStorage<Stores\NullStorage>
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createNullDriver(): ExtendedStorage
    {
        return $this->createDriver(Stores\NullStorage::class);
    }

    /**
     * @template T of StateStorage
     *
     * @param  class-string<T>  $driver
     * @param  array<string, mixed>  $config
     * @return ExtendedStorage<T>
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createDriver(string $driver, array $config = []): ExtendedStorage
    {
        return $this->container->make(ExtendedStorage::class, [
            'storage' => $this->container->make($driver, $config),
        ]);
    }
}
