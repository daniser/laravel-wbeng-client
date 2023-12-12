<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Stores;

use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\Contracts\StorableState;
use TTBooking\WBEngine\Exceptions\SessionNotFoundException;
use TTBooking\WBEngine\Exceptions\StateNotFoundException;

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

    public function hasSession(string $id): bool
    {
        return false;
    }

    public function session(string $id, ?string $queryType = null): never
    {
        throw new SessionNotFoundException('Null storage is always empty');
    }
}
