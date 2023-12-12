<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Contracts;

use Illuminate\Support\Enumerable;
use TTBooking\WBEngine\Exceptions\SessionNotFoundException;
use TTBooking\WBEngine\Exceptions\StateNotFoundException;
use TTBooking\WBEngine\QueryInterface;
use TTBooking\WBEngine\ResultInterface;

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

    /**
     * @deprecated
     */
    public function hasSession(string $id): bool;

    /**
     * @param  class-string<QueryInterface<ResultInterface>>|null  $queryType
     * @return Enumerable<string, StorableState<ResultInterface>>
     *
     * @throws SessionNotFoundException
     */
    public function session(string $id, ?string $queryType = null): Enumerable;
}
