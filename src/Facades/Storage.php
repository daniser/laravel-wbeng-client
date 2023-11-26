<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Facades;

use Illuminate\Support\Facades\Facade;
use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\State;

/**
 * @method static StateStorage connection(string $name = null)
 * @method static StateStorage[] getConnections()
 * @method static string store(State $state, State $parentState = null)
 * @method static State retrieve(string $id)
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
