<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
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

        static::status($result->context);

        if (! $result->flightGroups) {
            warning('No flights found.');

            return static::FAILURE;
        }

        $rows = static::collectData($result);
        $flightId = windows_os() || $this->option('table')
            ? static::displayTable($rows)
            : static::displaySelect($rows);

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
        return $this->argument('from') ?? static::location(
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
        return $this->argument('to') ?? static::location(
            label: 'To',
            prompter: $prompter,
            placeholder: 'LED|St. Petersburg',
            hint: 'Arrival location',
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

    protected static function status(ResponseContext $context): void
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
     * @return list<list<string>>
     */
    protected static function collectData(Response $result): array
    {
        $rows = [];
        /** @var list<Response\FlightGroup> $flightGroups */
        $flightGroups = array_values(Arr::sort($result->flightGroups, 'fares.fareTotal'));
        foreach ($flightGroups as $flightGroup) {
            $rows[] = [
                data_get($flightGroup, 'itineraries.^.flights.^.segments.^.dateBegin')->format('H:i'),
                data_get($flightGroup, 'itineraries.$.flights.$.segments.$.dateEnd')->format('H:i'),
                $flightGroup->carrier->code.' '.$flightGroup->provider.' '.$flightGroup->gds,
                data_get($flightGroup, 'itineraries.^.flights.^.segments.^.locationBegin.code').'-'.
                data_get($flightGroup, 'itineraries.$.flights.$.segments.$.locationEnd.code'),
                data_get($flightGroup, 'itineraries.0.flights.0.segments.0.carrier.code').'-'.
                data_get($flightGroup, 'itineraries.0.flights.0.segments.0.flightNumber'),
                $flightGroup->fares->fareTotal,
            ];
        }

        return $rows;
    }

    /**
     * @param  list<list<string>>  $rows
     */
    protected static function displayTable(array $rows): int
    {
        $rows = Arr::map($rows, static function (array $row, int $id) {
            $fareTotal = array_pop($row);

            return [self::rcell($id + 1), ...$row, self::rcell($fareTotal)];
        });

        table(['#', 'Departure', 'Arrival', 'Carrier/GDS', 'Route', 'Flight', 'Fare total'], $rows);

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
     * @param  list<list<string>>  $rows
     */
    protected static function displaySelect(array $rows): int
    {
        $rows = Arr::mapWithKeys($rows, static function (array $row, int $id) {
            return [$id + 1 => sprintf('%s  %s  %-13s  %s  %-7s  %7d', ...$row)];
        });

        return (int) select(
            label: 'Select flight',
            options: $rows,
            scroll: 25,
            hint: 'Choose flight to inspect its details',
        );
    }

    /**
     * @param  null|callable(string): array<string, string>  $prompter
     */
    protected static function location(string $label, callable $prompter = null, string $placeholder = '', string $hint = ''): string
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

    private static function rcell(string|int $value): TableCell
    {
        return new TableCell((string) $value, ['style' => new TableCellStyle(['align' => 'right'])]);
    }
}
