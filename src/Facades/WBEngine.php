<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Facades;

use Http\Promise\Promise;
use Illuminate\Support\Facades\Facade;
use TTBooking\WBEngine\Contracts\Client;
use TTBooking\WBEngine\DTO\Common\Request\Parameters as CommonParams;
use TTBooking\WBEngine\DTO\Common\Response;
use TTBooking\WBEngine\DTO\CreateBooking\Request\Parameters as BookingParams;
use TTBooking\WBEngine\DTO\CreateBooking\Response as BookingResponse;
use TTBooking\WBEngine\DTO\FlightFares\Response as FaresResponse;
use TTBooking\WBEngine\DTO\SearchFlights\Request\Parameters as SearchParams;
use TTBooking\WBEngine\DTO\SelectFlight\Request\Parameters as SelectParams;

/**
 * @method static Client connection(string $name = null)
 * @method static Client[] getConnections()
 * @method static Response searchFlights(SearchParams $parameters)
 * @method static Response selectFlight(SelectParams $parameters, string $provider = null, string $gds = null)
 * @method static BookingResponse createBooking(BookingParams $parameters, string $provider = null, string $gds = null)
 * @method static FaresResponse flightFares(CommonParams $parameters, string $provider = null, string $gds = null)
 * @method static Promise searchFlightsAsync(SearchParams $parameters)
 * @method static Promise selectFlightAsync(SelectParams $parameters, string $provider = null, string $gds = null)
 * @method static Promise createBookingAsync(BookingParams $parameters, string $provider = null, string $gds = null)
 * @method static Promise flightFaresAsync(CommonParams $parameters, string $provider = null, string $gds = null)
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
