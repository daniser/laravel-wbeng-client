<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use TTBooking\WBEngine\DTO\Air\Common\RequestContext;
use TTBooking\WBEngine\DTO\Air\Enums\RespondType;
use TTBooking\WBEngine\DTO\Air\FlightFares\Request\Parameters as FaresQuery;
use TTBooking\WBEngine\DTO\Air\FlightFares\Response as FaresResponse;
use TTBooking\WBEngine\DTO\Air\SearchFlights\Request\Parameters as SearchQuery;
use TTBooking\WBEngine\DTO\Air\SearchFlights\Response as SearchResponse;

/**
 * @extends Support\Manager<ClientInterface>
 */
class ConnectionManager extends Support\Manager implements ClientInterface, Contracts\ClientFactory
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

    /**
     * @param  array{
     *     driver: string,
     *     uri: string,
     *     login: string,
     *     password: string,
     *     salepoint: int[]|null,
     *     locale: string,
     *     respondType: RespondType,
     *     currency: string,
     *     id: int,
     *     provider: string,
     *     context_id: int|null,
     * }  $config
     *
     * @throws BindingResolutionException
     */
    protected function createDefaultDriver(array $config, string $connection): ClientInterface
    {
        unset($config['driver']);

        return $this->container->make(Client::class, [
            'baseUri' => Arr::pull($config, 'uri'),
            'context' => new RequestContext(...$config),
        ]);
    }
}
