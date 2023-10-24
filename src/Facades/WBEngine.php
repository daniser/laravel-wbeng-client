<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Facades;

use Illuminate\Support\Facades\Facade;
use TTBooking\WBEngine\ClientInterface;
use TTBooking\WBEngine\DTO\Common\Request\Parameters as CommonParams;
use TTBooking\WBEngine\DTO\CreateBooking\Request\Parameters as BookingParams;
use TTBooking\WBEngine\DTO\CreateBooking\Response as BookingResponse;
use TTBooking\WBEngine\DTO\FlightFares\Response as FaresResponse;
use TTBooking\WBEngine\DTO\SearchFlights\Request\Parameters as SearchParams;
use TTBooking\WBEngine\DTO\SearchFlights\Response as SearchResponse;
use TTBooking\WBEngine\DTO\SelectFlight\Response as SelectResponse;

/**
 * @method static ClientInterface connection(string $name = null)
 * @method static ClientInterface[] getConnections()
 * @method static SearchResponse searchFlights(SearchParams $parameters)
 * @method static SelectResponse selectFlight(CommonParams $parameters)
 * @method static BookingResponse createBooking(BookingParams $parameters)
 * @method static FaresResponse flightFares(CommonParams $parameters, string $provider, string $gds)
 *
 * @see \TTBooking\WBEngine\ConnectionManager
 */
class WBEngine extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'wbeng-client';
    }
}
