<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Contracts;

use TTBooking\WBEngine\Exceptions\StateNotFoundException;
use TTBooking\WBEngine\ResultInterface;
use TTBooking\WBEngine\StorableState;

interface StateStorage
{
    public function has(string $id): bool;

    /**
     * @return StorableState<ResultInterface>
     *
     * @throws StateNotFoundException
     */
    public function get(string $id): StorableState;

    /**
     * @param  StorableState<ResultInterface>  $state
     * @return StorableState<ResultInterface>
     */
    public function put(StorableState $state): StorableState;
}
