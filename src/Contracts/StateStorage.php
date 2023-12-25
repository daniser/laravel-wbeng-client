<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Contracts;

use Illuminate\Support\Enumerable;
use TTBooking\WBEngine\Exceptions\StateNotFoundException;
use TTBooking\WBEngine\Exceptions\UnsupportedConditionException;
use TTBooking\WBEngine\QueryInterface;
use TTBooking\WBEngine\ResultInterface;

interface StateStorage
{
    public function has(string $id): bool;

    /**
     * @return StorableState<ResultInterface, QueryInterface<ResultInterface>>
     *
     * @throws StateNotFoundException
     */
    public function get(string $id): StorableState;

    /**
     * @template TState of StorableState<ResultInterface, QueryInterface<ResultInterface>>
     *
     * @phpstan-param TState $state
     *
     * @phpstan-return TState
     */
    public function put(StorableState $state): StorableState;

    /**
     * @param  array<string, mixed>  $conditions
     * @return Enumerable<string, StorableState<ResultInterface, QueryInterface<ResultInterface>>>
     *
     * @throws UnsupportedConditionException
     */
    public function where(array $conditions): Enumerable;

    /**
     * @return Enumerable<string, StorableState<ResultInterface, QueryInterface<ResultInterface>>>
     */
    public function all(): Enumerable;
}
