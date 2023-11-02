<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use TTBooking\WBEngine\Facades\WBEngine;

function wbeng(string $connection = null): Client
{
    return WBEngine::connection($connection);
}

/**
 * Get an item from an array or object using "dot" notation.
 *
 * @param  string|array<string|int|null>|int|null  $key
 */
function data_get(mixed $target, string|array|int|null $key, mixed $default = null): mixed
{
    if (is_null($key)) {
        return $target;
    }

    $key = is_array($key) ? $key : explode('.', (string) $key);

    foreach ($key as $i => $segment) {
        unset($key[$i]);

        if (is_null($segment)) {
            return $target;
        }

        if ($segment === '*') {
            if ($target instanceof Collection) {
                $target = $target->all();
            } elseif (! is_iterable($target)) {
                return value($default);
            }

            $result = [];

            foreach ($target as $item) {
                $result[] = data_get($item, $key);
            }

            return in_array('*', $key) ? Arr::collapse($result) : $result;
        }

        $segment = match ($segment) {
            '^' => array_key_first($target),
            '$' => array_key_last($target),
            default => $segment,
        };

        if (Arr::accessible($target) && Arr::exists($target, $segment)) {
            $target = $target[$segment];
        } elseif (is_object($target) && isset($target->{$segment})) {
            $target = $target->{$segment};
        } else {
            return value($default);
        }
    }

    return $target;
}
