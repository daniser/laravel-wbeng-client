<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Stores;

use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\Exceptions\StateNotFoundException;
use TTBooking\WBEngine\StorableState;

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
}
