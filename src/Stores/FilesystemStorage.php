<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Stores;

use Illuminate\Support\Enumerable;
use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\Contracts\StorableState;
use TTBooking\WBEngine\Exceptions\StateNotFoundException;

class FilesystemStorage implements StateStorage
{
    public function __construct(protected string $path = 'wbeng/state')
    {
    }

    public function has(string $id): bool
    {
        // TODO: Implement has() method.

        return false;
    }

    public function get(string $id): StorableState
    {
        // TODO: Implement get() method.

        throw new StateNotFoundException;
    }

    public function put(StorableState $state): StorableState
    {
        // TODO: Implement put() method.

        return $state;
    }

    public function where(array $conditions): Enumerable
    {
        // TODO: Implement where() method.

        return collect();
    }

    public function all(): Enumerable
    {
        // TODO: Implement all() method.

        return collect();
    }
}
