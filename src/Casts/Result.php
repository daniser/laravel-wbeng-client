<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use TTBooking\WBEngine\EndpointQueryMap;
use TTBooking\WBEngine\Facades\Serializer;
use TTBooking\WBEngine\ResultInterface;

/**
 * @template TResult of ResultInterface
 *
 * @implements CastsAttributes<TResult, TResult>
 */
class Result implements CastsAttributes
{
    /** @var array<string, true> */
    protected array $context;

    public function __construct(string ...$arguments)
    {
        $this->context = array_fill_keys($arguments, true);
    }

    public function get(Model $model, string $key, mixed $value, array $attributes): ?ResultInterface
    {
        $type = EndpointQueryMap::getResultClassFromEndpoint($attributes['endpoint']);

        /** @var TResult */
        return Serializer::deserialize($value, $type, $this->context);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        return Serializer::serialize($value, $this->context);
    }
}
