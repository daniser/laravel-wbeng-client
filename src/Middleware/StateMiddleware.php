<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Middleware;

use Closure;
use TTBooking\WBEngine\QueryInterface;
use TTBooking\WBEngine\ResultInterface;
use TTBooking\WBEngine\State;

class StateMiddleware
{
    /**
     * @template TResult of ResultInterface
     *
     * @param  QueryInterface<TResult>  $query
     * @param  Closure(QueryInterface<TResult>): TResult  $next
     * @return State<TResult>
     */
    public function handle(QueryInterface $query, Closure $next): State
    {
        return new State($query, $next($query));
    }
}
