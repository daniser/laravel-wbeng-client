<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Casts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use TTBooking\WBEngine\EndpointQueryMap;
use TTBooking\WBEngine\ResultInterface;
use TTBooking\WBEngine\SerializerFactory;

/**
 * @template TResult of ResultInterface
 *
 * @implements CastsAttributes<TResult, TResult>
 */
class Result implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?ResultInterface
    {
        return SerializerFactory::discoverSerializer()
            ->deserialize($value, EndpointQueryMap::getQueryClassFromEndpoint($attributes['endpoint']));
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        return SerializerFactory::discoverSerializer()->serialize($value);
    }
}
