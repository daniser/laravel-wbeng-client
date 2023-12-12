<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Stores;

use Illuminate\Support\Enumerable;
use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\Contracts\StorableState;

class FilesystemStorage implements StateStorage
{
    public function __construct(protected string $path = 'wbeng/state')
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

    public function hasSession(string $id): bool
    {
        // TODO: Implement hasSession() method.
    }

    public function session(string $id, ?string $queryType = null): Enumerable
    {
        // TODO: Implement session() method.
    }
}
