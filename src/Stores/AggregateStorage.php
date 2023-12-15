<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Stores;

use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\Contracts\StorableState;
use TTBooking\WBEngine\Contracts\StorageFactory;
use TTBooking\WBEngine\Exceptions\AggregateStateNotFoundException;
use TTBooking\WBEngine\Exceptions\StateNotFoundException;

class AggregateStorage implements StateStorage
{
    /** @var Collection<int, StateStorage> */
    protected Collection $stores;

    /**
     * @param  StorageFactory<StateStorage>  $storageFactory
     * @param  list<string|null>  $stores
     */
    public function __construct(StorageFactory $storageFactory, array $stores = [])
    {
        $this->stores = collect($stores)->push('null')->unique()->map(
            static fn (?string $store) => $storageFactory->connection($store)
        );
    }

    public function has(string $id): bool
    {
        return $this->stores->some->has($id);
    }

    public function get(string $id): StorableState
    {
        $exceptions = [];

        foreach ($this->stores as $store) {
            try {
                return $store->get($id);
            } catch (StateNotFoundException $e) {
                $exceptions[] = $e;
            }
        }

        throw AggregateStateNotFoundException::withExceptions("State [$id] not found", $exceptions);
    }

    public function put(StorableState $state): StorableState
    {
        return $this->stores->each->put($state); // @phpstan-ignore-line
    }

    public function where(array $conditions): Enumerable
    {
        /** @noinspection PhpParamsInspection */
        return $this->stores->first->where($conditions); // @phpstan-ignore-line
    }

    public function all(): Enumerable
    {
        return $this->stores->first->all(); // @phpstan-ignore-line
    }
}
