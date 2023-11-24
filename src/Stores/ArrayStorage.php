<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Stores;

use Illuminate\Support\Str;
use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\Exceptions\StateNotFoundException;
use TTBooking\WBEngine\ResultInterface;
use TTBooking\WBEngine\StateInterface;

class ArrayStorage implements StateStorage
{
    /** @var array<string, StateInterface<ResultInterface>> */
    protected array $states = [];

    public function store(StateInterface $state, StateInterface $parentState = null): string
    {
        $id = (string) Str::orderedUuid();
        $this->states[$id] = $state;

        return $id;
    }

    public function retrieve(string $id): StateInterface
    {
        return $this->states[$id] ?? throw new StateNotFoundException("State [$id] not found");
    }
}
