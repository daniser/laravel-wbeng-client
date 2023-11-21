<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

/**
 * @template TResult of ResultInterface
 *
 * @implements StateInterface<TResult>
 */
class State implements StateInterface
{
    /**
     * @param  QueryInterface<TResult>  $query
     *
     * @phpstan-param  TResult $result
     */
    public function __construct(protected QueryInterface $query, protected ResultInterface $result)
    {
    }

    public function getQuery(): QueryInterface
    {
        return $this->query;
    }

    public function getResult(): ResultInterface
    {
        return $this->result;
    }
}
