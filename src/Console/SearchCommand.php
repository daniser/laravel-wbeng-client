<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use TTBooking\WBEngine\Contracts\ClientFactory;
use TTBooking\WBEngine\DTO\Air\SearchFlights\Response;
use function Laravel\Prompts\{info, note, spin, table, text};
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
        {--connection= : Using connection}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search flights';

    /**
     * Execute the console command.
     */
    public function handle(ClientFactory $clientFactory): void
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

        $rows = [];
        foreach ($result->flightGroups as $flightGroup) {
            $rows[] = [
                data_get($flightGroup, 'itineraries.^.flights.^.segments.^.dateBegin')->format('H:i'),
                data_get($flightGroup, 'itineraries.$.flights.$.segments.$.dateEnd')->format('H:i'),
                $flightGroup->carrier->code.' '.$flightGroup->provider.' '.$flightGroup->gds,
                data_get($flightGroup, 'itineraries.^.flights.^.segments.^.locationBegin.code').'-'.
                data_get($flightGroup, 'itineraries.$.flights.$.segments.$.locationEnd.code'),
                data_get($flightGroup, 'itineraries.0.flights.0.segments.0.carrier.code').'-'.
                data_get($flightGroup, 'itineraries.0.flights.0.segments.0.flightNumber'),
                new TableCell(
                    (string) $flightGroup->fares->fareTotal,
                    ['style' => new TableCellStyle(['align' => 'right'])]
                ),
            ];
        }

        table(['Departure', 'Arrival', 'Carrier/GDS', 'Route', 'Flight', 'Fare total'], $rows);

        info('Search successfully finished.');
    }
}
