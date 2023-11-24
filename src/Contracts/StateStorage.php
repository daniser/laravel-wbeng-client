<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Contracts;

use TTBooking\WBEngine\Exceptions\StateNotFoundException;
use TTBooking\WBEngine\ResultInterface;
use TTBooking\WBEngine\StateInterface;

interface StateStorage
{
    /**
     * @param  StateInterface<ResultInterface>  $state
     * @param  StateInterface<ResultInterface>|null  $parentState
     */
    public function store(StateInterface $state, StateInterface $parentState = null): string;

    /**
     * @return StateInterface<ResultInterface>
     *
     * @throws StateNotFoundException
     */
    public function retrieve(string $id): StateInterface;
}
