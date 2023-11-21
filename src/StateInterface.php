<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

/**
 * @template TResult of ResultInterface
 */
interface StateInterface extends ResultInterface
{
    /**
     * @return QueryInterface<TResult>
     */
    public function getQuery(): QueryInterface;

    /**
     * @phpstan-return TResult
     */
    public function getResult(): ResultInterface;
}
