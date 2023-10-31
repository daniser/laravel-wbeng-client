<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use TTBooking\WBEngine\DTO\Common\Request\Context;
use TTBooking\WBEngine\DTO\Common\Request\Parameters as CommonParams;
use TTBooking\WBEngine\DTO\Common\Response;
use TTBooking\WBEngine\DTO\CreateBooking\Request\Parameters as BookingParams;
use TTBooking\WBEngine\DTO\CreateBooking\Response as BookingResponse;
use TTBooking\WBEngine\DTO\Enums\RespondType;
use TTBooking\WBEngine\DTO\FlightFares\Response as FaresResponse;
use TTBooking\WBEngine\DTO\SearchFlights\Request\Parameters as SearchParams;
use TTBooking\WBEngine\DTO\SelectFlight\Request\Parameters as SelectParams;
use UnexpectedValueException;

/**
 * @extends Support\Manager<ClientInterface>
 */
class ConnectionManager extends Support\Manager implements ClientInterface, Contracts\ClientFactory
{
    protected string $configName = 'wbeng-client';

    public function searchFlights(SearchParams $parameters): Response
    {
        return $this->connection()->searchFlights($parameters);
    }

    public function selectFlight(SelectParams $parameters, string $provider = null, string $gds = null): Response
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
     *     provider: string,
     *     salePoint: string|null,
     *     currency: string,
     *     locale: string,
     *     respondType: RespondType,
     *     id: int,
     *     context_id: int|null,
     *     serializer: 'symfony'|'jms'|'default'|null
     * }  $config
     *
     * @throws BindingResolutionException
     */
    protected function createDefaultDriver(array $config, string $connection): ClientInterface
    {
        unset($config['driver']);

        /** @var string|null $serializer */
        $serializer = Arr::pull($config, 'serializer');

        return $this->container->make(Client::class, [
            'baseUri' => Arr::pull($config, 'uri'),
            'context' => new Context(...$config),
            'serializer' => match ($serializer) {
                'symfony' => SerializerFactory::createSymfonySerializer(),
                'jms' => SerializerFactory::createJMSSerializer(),
                'default', null => null,
                default => throw new UnexpectedValueException("Invalid serializer [$serializer]."),
            },
        ]);
    }
}
