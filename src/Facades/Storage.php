<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Facades;

use Illuminate\Support\Facades\Facade;
use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\Contracts\StorableState;
use TTBooking\WBEngine\Session;

/**
 * @method static StateStorage connection(string $name = null)
 * @method static StateStorage[] getConnections()
 * @method static bool has(string $id)
 * @method static StorableState get(string $id)
 * @method static StorableState put(StorableState $state)
 * @method static Session session(string $id)
 *
 * @see \TTBooking\WBEngine\StorageManager
 */
class Storage extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'wbeng-store';
    }
}
