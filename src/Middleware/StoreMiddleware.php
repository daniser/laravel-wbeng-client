<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Middleware;

use Closure;
use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\Contracts\StorableState;
use TTBooking\WBEngine\Contracts\StorageFactory;
use TTBooking\WBEngine\QueryInterface;
use TTBooking\WBEngine\ResultInterface;

class StoreMiddleware
{
    /**
     * @param  StorageFactory<StateStorage>  $storageFactory
     */
    public function __construct(protected StorageFactory $storageFactory)
    {
    }

    /**
     * @template TResult of ResultInterface
     * @template TQuery of QueryInterface<TResult>
     *
     * @phpstan-param  TQuery $query
     *
     * @param  Closure(TQuery): StorableState<TResult, TQuery>  $next
     * @return StorableState<TResult, TQuery>
     */
    public function handle(QueryInterface $query, Closure $next, ?string $connection = null): StorableState
    {
        return $this->storageFactory->connection($connection)->put($next($query));
    }
}
