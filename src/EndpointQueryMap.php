<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use Illuminate\Support\Arr;
use UnexpectedValueException;

final class EndpointQueryMap
{
    /** @var list<class-string<QueryInterface<ResultInterface>>> */
    private const QUERIES = [
        DTO\SearchFlights\Query::class,
        DTO\SelectFlight\Query::class,
        DTO\FlightFares\Query::class,
        DTO\CreateBooking\Query::class,
    ];

    /** @var array<string, class-string<QueryInterface<ResultInterface>>> */
    private static array $map;

    /**
     * @return class-string<QueryInterface<ResultInterface>>
     */
    public static function getQueryClassFromEndpoint(string $endpoint): string
    {
        self::$map ??= self::buildMapFromQueries();

        return self::$map[$endpoint] ?? throw new UnexpectedValueException("Endpoint [$endpoint] doesn't exist");
    }

    /**
     * @return class-string<ResultInterface>
     */
    public static function getResultClassFromEndpoint(string $endpoint): string
    {
        return self::getQueryClassFromEndpoint($endpoint)::getResultType();
    }

    /**
     * @return array<string, class-string<QueryInterface<ResultInterface>>>
     */
    private static function buildMapFromQueries(): array
    {
        return Arr::mapWithKeys(self::QUERIES, static function (string $queryClass) {
            return [$queryClass::getEndpoint() => $queryClass];
        });
    }
}
