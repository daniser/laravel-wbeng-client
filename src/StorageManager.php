<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

/**
 * @extends Support\Manager<Contracts\StateStorage>
 */
class StorageManager extends Support\Manager implements Contracts\StateStorage, Contracts\StorageFactory
{
    protected string $configName = 'wbeng-client';

    public function store(StateInterface $state, StateInterface $parentState = null): string
    {
        return $this->connection()->store($state, $parentState);
    }

    public function retrieve(string $id): StateInterface
    {
        return $this->connection()->retrieve($id);
    }

    /**
     * @param array{
     *     driver: string,
     *     model: class-string<Models\State>,
     * } $config
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createEloquentDriver(array $config, string $connection): Stores\EloquentStorage
    {
        unset($config['driver']);

        return $this->container->make(Stores\EloquentStorage::class, $config);
    }

    /**
     * @param array{
     *     driver: string,
     *     table: string,
     * } $config
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createDatabaseDriver(array $config, string $connection): Stores\DatabaseStorage
    {
        unset($config['driver']);

        return $this->container->make(Stores\DatabaseStorage::class, $config);
    }

    /**
     * @param array{
     *     driver: string,
     *     path: string,
     * } $config
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createFilesystemDriver(array $config, string $connection): Stores\FilesystemStorage
    {
        unset($config['driver']);

        return $this->container->make(Stores\FilesystemStorage::class, $config);
    }

    /**
     * @param  array{driver: string}  $config
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createArrayDriver(array $config, string $connection): Stores\ArrayStorage
    {
        return $this->container->make(Stores\ArrayStorage::class);
    }

    /**
     * @param  array{driver: string}  $config
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createNullDriver(array $config, string $connection): Stores\NullStorage
    {
        return $this->container->make(Stores\NullStorage::class);
    }
}
