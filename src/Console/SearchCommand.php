<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use TTBooking\WBEngine\Contracts\ClientFactory;
use TTBooking\WBEngine\DTO\Air\Common\ResponseContext;
use TTBooking\WBEngine\DTO\Air\SearchFlights\Response;

use function Laravel\Prompts\{info, note, search, select, spin, table, text, warning};
use function TTBooking\WBEngine\{data_get, fly};

#[AsCommand(
    name: 'wbeng:search',
    description: 'Search flights',
)]
class SearchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wbeng:search
        {from? : Origin location code}
        {to? : Destination location code}
        {date? : Flight date}
        {--connection= : Using connection}
        {--table : Present results in table form}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search flights';

    /**
     * Execute the console command.
     */
    public function handle(ClientFactory $clientFactory): int
    {
        /** @var null|callable(string): array<string, string> $prompter */
        $prompter = config('wbeng-client.iata_location_prompter');

        $result = $this->searchFlights(
            clientFactory: $clientFactory,
            origin: $this->getDepartureLocation($prompter),
            destination: $this->getArrivalLocation($prompter),
            date: $this->getDepartureDate(),
        );

        static::displayStatus($result->context);

        if (! $result->flightGroups) {
            warning('No flights found.');

            return static::FAILURE;
        }

        $flightId = $this->displayFlights($result->flightGroups);

        info(gettype($flightId));
        info((string) $flightId);

        info('Search successfully finished.');

        return static::SUCCESS;
    }

    /**
     * @param  null|callable(string): array<string, string>  $prompter
     */
    protected function getDepartureLocation(callable $prompter = null): string
    {
        /** @var string */
        return $this->argument('from') ?? static::getLocation(
            label: 'From',
            prompter: $prompter,
            placeholder: 'MOW|Moscow',
            hint: 'Departure location',
        );
    }

    /**
     * @param  null|callable(string): array<string, string>  $prompter
     */
    protected function getArrivalLocation(callable $prompter = null): string
    {
        /** @var string */
        return $this->argument('to') ?? static::getLocation(
            label: 'To',
            prompter: $prompter,
            placeholder: 'LED|St. Petersburg',
            hint: 'Arrival location',
        );
    }

    /**
     * @param  null|callable(string): array<string, string>  $prompter
     */
    protected static function getLocation(string $label, callable $prompter = null, string $placeholder = '', string $hint = ''): string
    {
        $parts = explode('|', $placeholder);

        return $prompter
            ? (string) search(
                label: $label,
                options: $prompter(...),
                placeholder: $parts[1] ?? $parts[0],
                hint: $hint,
            ) : text(
                label: $label,
                placeholder: $parts[0],
                required: true,
                hint: $hint.' code',
            );
    }

    protected function getDepartureDate(): string
    {
        /** @var string */
        return $this->argument('date') ?? text(
            label: 'Date',
            placeholder: date('Y-m-d'),
            required: true,
            hint: 'Date of departure',
        );
    }

    protected function searchFlights(ClientFactory $clientFactory, string $origin, string $destination, string $date): Response
    {
        /** @var string $connection */
        $connection = $this->option('connection');

        return spin(fn (): Response => $clientFactory->connection($connection)->searchFlights(
            fly()->from($origin)->to($destination)->at($date)
        ), 'Searching flights...');
    }

    protected static function displayStatus(ResponseContext $context): void
    {
        note(sprintf(
            "<question>%s</question>\t\t%s\t\t<comment>%s</comment>\t\t<info>%s</info>",
            'WBENG '.$context->version,
            $context->environment,
            $context->profile,
            implode(',', $context->provider),
        ));
    }

    /**
     * @param  list<Response\FlightGroup>  $flightGroups
     */
    protected function displayFlights(array $flightGroups): int
    {
        $rows = static::collectData($flightGroups);

        return windows_os() || $this->option('table')
            ? static::displayTable($rows)
            : static::displaySelect($rows);
    }

    /**
     * @param  list<Response\FlightGroup>  $flightGroups
     * @return Collection<int, list<string>>
     */
    protected static function collectData(array $flightGroups): Collection
    {
        return collect($flightGroups)->sortBy('fares.fareTotal')->values()->map(self::extractRow(...));
    }

    /**
     * @return list<string>
     */
    private static function extractRow(Response\FlightGroup $flightGroup): array
    {
        return [

            /** @phpstan-ignore-next-line */
            (string) data_get($flightGroup, 'itineraries.^.flights.^.segments.^.dateBegin')->format('H:i'),

            /** @phpstan-ignore-next-line */
            (string) data_get($flightGroup, 'itineraries.$.flights.$.segments.$.dateEnd')->format('H:i'),

            $flightGroup->carrier->code.' '.$flightGroup->provider.' '.$flightGroup->gds,

            data_get($flightGroup, 'itineraries.^.flights.^.segments.^.locationBegin.code').'-'.
            data_get($flightGroup, 'itineraries.$.flights.$.segments.$.locationEnd.code'),

            data_get($flightGroup, 'itineraries.0.flights.0.segments.0.carrier.code').'-'.
            data_get($flightGroup, 'itineraries.0.flights.0.segments.0.flightNumber'),

            (string) $flightGroup->fares->fareTotal,

        ];
    }

    /**
     * @param  Collection<int, list<string>>  $rows
     */
    protected static function displayTable(Collection $rows): int
    {
        table(
            ['#', 'Departure', 'Arrival', 'Carrier/GDS', 'Route', 'Flight', 'Fare total'],
            /** @phpstan-ignore-next-line */
            $rows->map(self::formatTableRow(...))
        );

        return (int) text(
            label: 'Enter flight #',
            required: true,
            validate: static fn (string $value) => filter_var($value, FILTER_VALIDATE_INT, ['options' => [
                'min_range' => 1,
                'max_range' => count($rows),
            ]]) === false ? 'Please enter valid flight #' : null,
            hint: 'Choose flight to inspect its details',
        );
    }

    /**
     * @param  non-empty-list<string>  $row
     * @return list<string|TableCell>
     */
    private static function formatTableRow(array $row, int $id): array
    {
        $fareTotal = array_pop($row);

        return [self::alignRight($id + 1), ...$row, self::alignRight($fareTotal)];
    }

    private static function alignRight(string|int $value): TableCell
    {
        return new TableCell((string) $value, ['style' => new TableCellStyle(['align' => 'right'])]);
    }

    /**
     * @param  Collection<int, list<string>>  $rows
     */
    protected static function displaySelect(Collection $rows): int
    {
        return (int) select(
            label: 'Select flight',
            options: $rows->mapWithKeys(self::formatSelectRow(...)),
            scroll: 25,
            hint: 'Choose flight to inspect its details',
        );
    }

    /**
     * @param  list<string>  $row
     * @return array<string>
     */
    private static function formatSelectRow(array $row, int $id): array
    {
        return [$id + 1 => sprintf('%s  %s  %-13s  %s  %-7s  %7d', ...$row)];
    }
}
