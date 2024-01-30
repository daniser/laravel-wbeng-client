<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use TTBooking\WBEngine\EndpointQueryMap;
use TTBooking\WBEngine\Facades\Serializer;
use TTBooking\WBEngine\QueryInterface;
use TTBooking\WBEngine\ResultInterface;

/**
 * @template TQuery of QueryInterface<ResultInterface>
 *
 * @implements CastsAttributes<TQuery, TQuery>
 */
class Query implements CastsAttributes
{
    /** @var array<string, true> */
    protected array $context;

    public function __construct(string ...$arguments)
    {
        $this->context = array_fill_keys($arguments, true);
    }

    public function get(Model $model, string $key, mixed $value, array $attributes): ?QueryInterface
    {
        $type = EndpointQueryMap::getQueryClassFromEndpoint($attributes['endpoint']);

        /** @var TQuery */
        return Serializer::deserialize($value, $type, $this->context);
    }

    /**
     * @return array{endpoint: string, query: string}
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        return [
            'endpoint' => $value::getEndpoint(),
            'query' => Serializer::serialize($value, $this->context),
        ];
    }
}
