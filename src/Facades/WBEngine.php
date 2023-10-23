<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Facades;

use Illuminate\Support\Facades\Facade;
use TTBooking\WBEngine\ClientInterface;
use TTBooking\WBEngine\DTO\Air\Common\Request\Parameters as CommonQuery;
use TTBooking\WBEngine\DTO\Air\CreateBooking\Request\Parameters as BookingQuery;
use TTBooking\WBEngine\DTO\Air\CreateBooking\Response as BookingResponse;
use TTBooking\WBEngine\DTO\Air\FlightFares\Response as FaresResponse;
use TTBooking\WBEngine\DTO\Air\SearchFlights\Request\Parameters as SearchQuery;
use TTBooking\WBEngine\DTO\Air\SearchFlights\Response as SearchResponse;
use TTBooking\WBEngine\DTO\Air\SelectFlight\Response as SelectResponse;

/**
 * @method static ClientInterface connection(string $name = null)
 * @method static ClientInterface[] getConnections()
 * @method static SearchResponse searchFlights(SearchQuery $query)
 * @method static SelectResponse selectFlight(CommonQuery $query)
 * @method static BookingResponse createBooking(BookingQuery $query)
 * @method static FaresResponse flightFares(CommonQuery $query, string $provider, string $gds)
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
