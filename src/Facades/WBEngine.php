<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Facades;

use Http\Promise\Promise;
use Illuminate\Support\Facades\Facade;
use TTBooking\WBEngine\ClientInterface;
use TTBooking\WBEngine\QueryInterface;
use TTBooking\WBEngine\StateInterface;

/**
 * @method static ClientInterface connection(string $name = null)
 * @method static ClientInterface[] getConnections()
 * @method static StateInterface query(QueryInterface $query)
 * @method static Promise asyncQuery(QueryInterface $query)
 *
 * @see \TTBooking\WBEngine\ConnectionManager
 */
class WBEngine extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'wbeng-client';
    }
}
