<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Stores;

use Illuminate\Support\Enumerable;
use TTBooking\WBEngine\Contracts\StorableState;
use TTBooking\WBEngine\Exceptions\StateNotFoundException;

class NullStorage extends StateStorage
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

    protected function getSessionHistory(string $id): Enumerable
    {
        return collect();
    }
}
