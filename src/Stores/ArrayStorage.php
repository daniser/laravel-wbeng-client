<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Stores;

use Illuminate\Support\Str;
use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\Exceptions\StateNotFoundException;
use TTBooking\WBEngine\ResultInterface;
use TTBooking\WBEngine\StorableState;

class ArrayStorage implements StateStorage
{
    /** @var array<string, StorableState<ResultInterface>> */
    protected array $states = [];

    public function has(string $id): bool
    {
        return isset($this->states[$id]);
    }

    public function get(string $id): StorableState
    {
        return $this->states[$id] ?? throw new StateNotFoundException("State [$id] not found");
    }

    public function put(StorableState $state): StorableState
    {
        $id = (string) Str::orderedUuid();

        return $this->states[$id] = $state->id($id);
    }
}
