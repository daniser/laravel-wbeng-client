<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Stores;

use Illuminate\Support\Collection;
use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\Contracts\StorableState;
use TTBooking\WBEngine\Exceptions\StateNotFoundException;
use TTBooking\WBEngine\Exceptions\UnsupportedConditionException;
use TTBooking\WBEngine\QueryInterface;
use TTBooking\WBEngine\ResultInterface;

class NullStorage implements StateStorage
{
    public function has(string $id): bool
    {
        return false;
    }

    public function get(string $id): never
    {
        throw new StateNotFoundException('Null storage is always empty');
    }

    public function put(StorableState $state): StorableState
    {
        return $state;
    }

    /**
     * @return Collection<string, StorableState<ResultInterface, QueryInterface<ResultInterface>>>
     */
    public function where(array $conditions): Collection
    {
        if ($conditions) {
            throw new UnsupportedConditionException("Null storage doesn't support conditions");
        }

        return collect();
    }

    /**
     * @return Collection<string, StorableState<ResultInterface, QueryInterface<ResultInterface>>>
     */
    public function all(): Collection
    {
        return collect();
    }
}
