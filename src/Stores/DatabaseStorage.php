<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Stores;

use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\StateInterface;

class DatabaseStorage implements StateStorage
{
    public function __construct(protected string $table = 'wbeng_state')
    {
    }

    public function store(StateInterface $state, StateInterface $parentState = null): string
    {
        // TODO: Implement store() method.
    }

    public function retrieve(string $id): StateInterface
    {
        // TODO: Implement retrieve() method.
    }
}
