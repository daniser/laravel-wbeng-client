<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Stores;

use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\State;

class DatabaseStorage implements StateStorage
{
    public function __construct(protected string $table = 'wbeng_state')
    {
    }

    public function store(State $state, State $parentState = null): string
    {
        // TODO: Implement store() method.
    }

    public function retrieve(string $id): State
    {
        // TODO: Implement retrieve() method.
    }
}
