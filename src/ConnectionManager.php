<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use TTBooking\WBEngine\DTO\Common\Request\Context;
use TTBooking\WBEngine\DTO\Common\Request\Parameters as CommonParams;
use TTBooking\WBEngine\DTO\CreateBooking\Request\Parameters as BookingParams;
use TTBooking\WBEngine\DTO\CreateBooking\Response as BookingResponse;
use TTBooking\WBEngine\DTO\Enums\RespondType;
use TTBooking\WBEngine\DTO\FlightFares\Response as FaresResponse;
use TTBooking\WBEngine\DTO\SearchFlights\Request\Parameters as SearchParams;
use TTBooking\WBEngine\DTO\SearchFlights\Response as SearchResponse;
use TTBooking\WBEngine\DTO\SelectFlight\Request\Parameters as SelectParams;
use TTBooking\WBEngine\DTO\SelectFlight\Response as SelectResponse;

/**
 * @extends Support\Manager<ClientInterface>
 */
class ConnectionManager extends Support\Manager implements ClientInterface, Contracts\ClientFactory
{
    protected string $configName = 'wbeng-client';

    public function searchFlights(SearchParams $parameters): SearchResponse
    {
        return $this->connection()->searchFlights($parameters);
    }

    public function selectFlight(SelectParams $parameters, string $provider = null, string $gds = null): SelectResponse
    {
        return $this->connection()->selectFlight($parameters);
    }

    public function createBooking(BookingParams $parameters, string $provider = null, string $gds = null): BookingResponse
    {
        return $this->connection()->createBooking($parameters);
    }

    public function flightFares(CommonParams $parameters, string $provider = null, string $gds = null): FaresResponse
    {
        return $this->connection()->flightFares($parameters, $provider, $gds);
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
            'context' => new Context(...$config),
        ]);
    }
}
