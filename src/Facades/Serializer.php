<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string serialize(mixed $data, array $context = [])
 * @method static object deserialize(string $data, string $type, array $context = [])
 *
 * @see \TTBooking\WBEngine\Serializer
 */
class Serializer extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'wbeng-serializer';
    }
}
