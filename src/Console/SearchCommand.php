<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use TTBooking\WBEngine\Contracts\ClientFactory;
use TTBooking\WBEngine\DTO\Air\SearchFlights\Response;

use function Laravel\Prompts\{info, note, select, spin, table, text, warning};
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
        $origin = $this->argument('from') ?? text(
            label: 'From',
            placeholder: 'MOW',
            required: true,
            hint: 'Departure location code',
        );

        $destination = $this->argument('to') ?? text(
            label: 'To',
            placeholder: 'LED',
            required: true,
            hint: 'Arrival location code',
        );

        $date = $this->argument('date') ?? text(
            label: 'Date',
            placeholder: date('Y-m-d'),
            required: true,
            hint: 'Date of departure',
        );

        $result = spin(fn (): Response => $clientFactory->connection($this->option('connection'))->searchFlights(
            fly()->from($origin)->to($destination)->at($date)
        ), 'Searching flights...');

        note(sprintf(
            "<question>%s</question>\t\t%s\t\t<comment>%s</comment>\t\t<info>%s</info>",
            'WBENG '.$result->context->version,
            $result->context->environment,
            $result->context->profile,
            implode(',', $result->context->provider),
        ));

        if (! $result->flightGroups) {
            warning('No flights found.');

            return static::FAILURE;
        }

        $rows = [];
        $table = windows_os() || $this->option('table');
        $flightGroups = array_values(Arr::sort($result->flightGroups, 'fares.fareTotal'));
        foreach ($flightGroups as $id => $flightGroup) {
            $row = [
                data_get($flightGroup, 'itineraries.^.flights.^.segments.^.dateBegin')->format('H:i'),
                data_get($flightGroup, 'itineraries.$.flights.$.segments.$.dateEnd')->format('H:i'),
                $flightGroup->carrier->code.' '.$flightGroup->provider.' '.$flightGroup->gds,
                data_get($flightGroup, 'itineraries.^.flights.^.segments.^.locationBegin.code').'-'.
                data_get($flightGroup, 'itineraries.$.flights.$.segments.$.locationEnd.code'),
                data_get($flightGroup, 'itineraries.0.flights.0.segments.0.carrier.code').'-'.
                data_get($flightGroup, 'itineraries.0.flights.0.segments.0.flightNumber'),
                $table ? self::rcell($flightGroup->fares->fareTotal) : $flightGroup->fares->fareTotal,
            ];

            if ($table) {
                $rows[] = [self::rcell($id + 1), ...$row];
            } else {
                $rows[$id + 1] = sprintf('%s  %s  %-11s  %s  %-7s  %7d', ...$row);
            }
        }

        if ($table) {
            table(['#', 'Departure', 'Arrival', 'Carrier/GDS', 'Route', 'Flight', 'Fare total'], $rows);

            $flightId = (int) text(
                label: 'Enter flight #',
                required: true,
                validate: static fn (string $value) => filter_var($value, FILTER_VALIDATE_INT, ['options' => [
                    'min_range' => 1,
                    'max_range' => count($rows),
                ]]) === false ? 'Please enter valid flight #' : null,
                hint: 'Choose flight to inspect its details',
            );
        } else {
            $flightId = select(
                label: 'Select flight',
                options: $rows,
                scroll: 25,
                hint: 'Choose flight to inspect its details',
            );
        }

        info(gettype($flightId));
        info((string) $flightId);

        info('Search successfully finished.');

        return static::SUCCESS;
    }

    private static function rcell(string|int $value): TableCell
    {
        return new TableCell((string) $value, ['style' => new TableCellStyle(['align' => 'right'])]);
    }
}
