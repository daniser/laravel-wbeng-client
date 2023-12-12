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
     *
     * @param  QueryInterface<TResult>  $query
     * @param  Closure(QueryInterface<TResult>): StateInterface<TResult>  $next
     * @return StateInterface<TResult>
     */
    public function handle(QueryInterface $query, Closure $next): StateInterface
    {
        return $next($query);
    }
}
