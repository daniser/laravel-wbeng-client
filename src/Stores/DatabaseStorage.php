<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Stores;

use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\StorableState;

class DatabaseStorage implements StateStorage
{
    public function __construct(protected string $table = 'wbeng_state')
    {
    }

    public function has(string $id): bool
    {
        // TODO: Implement has() method.
    }

    public function get(string $id): StorableState
    {
        // TODO: Implement get() method.
    }

    public function put(StorableState $state): StorableState
    {
        // TODO: Implement put() method.
    }
}
