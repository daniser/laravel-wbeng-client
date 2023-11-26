<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Contracts;

use TTBooking\WBEngine\Exceptions\StateNotFoundException;
use TTBooking\WBEngine\ResultInterface;
use TTBooking\WBEngine\State;

interface StateStorage
{
    /**
     * @param  State<ResultInterface>  $state
     * @param  State<ResultInterface>|null  $parentState
     */
    public function store(State $state, State $parentState = null): string;

    /**
     * @return State<ResultInterface>
     *
     * @throws StateNotFoundException
     */
    public function retrieve(string $id): State;
}
