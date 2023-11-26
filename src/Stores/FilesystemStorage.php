<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Stores;

use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\State;

class FilesystemStorage implements StateStorage
{
    public function __construct(protected string $path = 'wbeng/state')
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
