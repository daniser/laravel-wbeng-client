<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Middleware;

use Closure;
use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\Contracts\StorableState;
use TTBooking\WBEngine\QueryInterface;
use TTBooking\WBEngine\ResultInterface;

class StoreMiddleware
{
    public function __construct(protected StateStorage $storage)
    {
    }

    /**
     * @template TResult of ResultInterface
     *
     * @param  QueryInterface<TResult>  $query
     * @param  Closure(QueryInterface<TResult>): StorableState<TResult>  $next
     * @return StorableState<TResult>
     */
    public function handle(QueryInterface $query, Closure $next): StorableState
    {
        $state = $next($query);

        return $this->storage->put($state);
    }
}
