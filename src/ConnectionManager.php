<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use TTBooking\WBEngine\DTO\Air\Common\RequestContext;
use TTBooking\WBEngine\DTO\Air\FlightFares\Request\Parameters as FaresQuery;
use TTBooking\WBEngine\DTO\Air\FlightFares\Response as FaresResponse;
use TTBooking\WBEngine\DTO\Air\SearchFlights\Request\Parameters as SearchQuery;
use TTBooking\WBEngine\DTO\Air\SearchFlights\Response as SearchResponse;

/**
 * @extends Support\Manager<ClientInterface>
 */
class ConnectionManager extends Support\Manager implements ClientInterface
{
    protected string $configName = 'wbeng-client';

    public function searchFlights(SearchQuery $query): SearchResponse
    {
        return $this->connection()->searchFlights($query);
    }

    public function flightFares(FaresQuery $query, string $provider, string $gds): FaresResponse
    {
        return $this->connection()->flightFares($query, $provider, $gds);
    }

    protected function createDefaultDriver(array $config, string $connection): ClientInterface
    {
        return $this->container->make(Client::class, [
            'baseUri' => $config['uri'],
            'context' => new RequestContext(...$config),
        ]);
    }
}
