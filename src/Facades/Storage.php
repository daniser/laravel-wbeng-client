<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Facades;

use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\Facade;
use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\Contracts\StorableState;

/**
 * @method static StateStorage connection(string $name = null)
 * @method static StateStorage[] getConnections()
 * @method static bool has(string $id)
 * @method static StorableState get(string $id)
 * @method static StorableState put(StorableState $state)
 * @method static bool hasSession(string $id)
 * @method static Enumerable session(string $id, string $queryType = null)
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
