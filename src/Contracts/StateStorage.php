<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Contracts;

use Illuminate\Support\Enumerable;
use TTBooking\WBEngine\Exceptions\StateNotFoundException;
use TTBooking\WBEngine\Exceptions\UnsupportedConditionException;
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
     * @param  array<string, mixed>  $conditions
     * @return Enumerable<string, StorableState<ResultInterface>>
     *
     * @throws UnsupportedConditionException
     */
    public function where(array $conditions): Enumerable;

    /**
     * @return Enumerable<string, StorableState<ResultInterface>>
     */
    public function all(): Enumerable;
}
