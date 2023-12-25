<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Middleware;

use Closure;
use TTBooking\WBEngine\QueryInterface;
use TTBooking\WBEngine\ResultInterface;
use TTBooking\WBEngine\StateInterface;

class StateMiddleware
{
    /**
     * @template TResult of ResultInterface
     * @template TQuery of QueryInterface<TResult>
     *
     * @phpstan-param  TQuery $query
     *
     * @param  Closure(TQuery): StateInterface<TResult, TQuery>  $next
     * @return StateInterface<TResult, TQuery>
     */
    public function handle(QueryInterface $query, Closure $next): StateInterface
    {
        return $next($query);
    }
}
