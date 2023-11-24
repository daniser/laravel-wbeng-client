<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use TTBooking\WBEngine\EndpointQueryMap;
use TTBooking\WBEngine\QueryInterface;
use TTBooking\WBEngine\ResultInterface;
use TTBooking\WBEngine\SerializerFactory;

/**
 * @template TQuery of QueryInterface<ResultInterface>
 *
 * @implements CastsAttributes<TQuery, TQuery>
 */
class Query implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?QueryInterface
    {
        return SerializerFactory::discoverSerializer()
            ->deserialize($value, EndpointQueryMap::getQueryClassFromEndpoint($attributes['endpoint']));
    }

    /**
     * @return array{endpoint: string, query: string}
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        return [
            'endpoint' => $value::getEndpoint(),
            'query' => SerializerFactory::discoverSerializer()->serialize($value),
        ];
    }
}
