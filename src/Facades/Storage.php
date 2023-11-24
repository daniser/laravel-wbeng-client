<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Facades;

use Illuminate\Support\Facades\Facade;
use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\StateInterface;

/**
 * @method static StateStorage connection(string $name = null)
 * @method static StateStorage[] getConnections()
 * @method static string store(StateInterface $state, StateInterface $parentState = null)
 * @method static StateInterface retrieve(string $id)
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
