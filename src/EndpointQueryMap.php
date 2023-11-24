<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use Illuminate\Support\Arr;

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

    public static function getQueryClassFromEndpoint(string $endpoint): ?string
    {
        self::$map ??= self::buildMapFromQueries();

        return self::$map[$endpoint] ?? null;
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
