<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Stores;

use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\Exceptions\StateNotFoundException;
use TTBooking\WBEngine\StateInterface;

class NullStorage implements StateStorage
{
    public function store(StateInterface $state, StateInterface $parentState = null): string
    {
        return '';
    }

    public function retrieve(string $id): never
    {
        throw new StateNotFoundException('Null storage is always empty');
    }
}
