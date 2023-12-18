<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use TTBooking\WBEngine\ClientInterface;
use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\Contracts\StorableState;
use TTBooking\WBEngine\Contracts\StorageFactory;
use TTBooking\WBEngine\DTO\Common\Result;
use TTBooking\WBEngine\ExtendedStorage;

use function Laravel\Prompts\{note, select, spin, table, text, warning};
use function TTBooking\WBEngine\Functional\do\choose;
use function TTBooking\WBEngine\data_get;

#[AsCommand(
    name: 'wbeng:select',
    description: 'Select flight',
)]
class SelectCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wbeng:select
        {session : Session identifier}
        {--connection= : Using connection}
        {--table : Present results in table form}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Select flight';

    /**
     * Execute the console command.
     *
     * @param  StorageFactory<ExtendedStorage<StateStorage>>  $storageFactory
     */
    public function handle(StorageFactory $storageFactory): int
    {
        /** @var string $session */
        $session = $this->argument('session');
        $store = $storageFactory->connection();
        $client = $store->session($session);

        /** @var StorableState<Result>|null $state */
        $state = $store->where([StorableState::ATTR_SESSION_ID => $session])->first();

        if (! $state) {
            warning('Session not found.');

            return static::FAILURE;
        }

        $searchResult = $state->getResult();

        static::displayStatus($searchResult->context);
        static::displayMessages($searchResult->messages);

        if (! $searchResult->flightGroups) {
            warning('No flights found.');

            return static::FAILURE;
        }

        $flightGroupId = $this->displayFlights($searchResult->flightGroups);

        $selectResult = $this->selectFlight(
            client: $client,
            searchResult: $searchResult,
            flightGroupId: $flightGroupId,
            itineraryId: 0,
            flightId: 0,
        );

        static::displayStatus($selectResult->context);
        static::displayMessages($selectResult->messages);

        if (! $selectResult->flightGroups) {
            warning('No flights found.');

            return static::FAILURE;
        }

        $this->displayFlights($selectResult->flightGroups);

        return static::SUCCESS;
    }

    protected function selectFlight(ClientInterface $client, Result $searchResult, int $flightGroupId, int $itineraryId, int $flightId): Result
    {
        return spin(fn (): Result => $client->query(
            choose()->fromSearchResult($searchResult, $flightGroupId, $itineraryId, $flightId)
        )->getResult(), 'Checking availability...');
    }

    protected static function displayStatus(Result\Context $context): void
    {
        note(sprintf(
            "<question>%s</question>\t\t%s\t\t<comment>%s</comment>\t\t<info>%s</info>",
            'WBENG '.$context->version,
            $context->environment,
            $context->profile,
            implode(',', isset($context->provider) ? (array) $context->provider : array_keys($context->executionTimeReport)),
        ));
    }

    /**
     * @param  list<Result\Message>  $messages
     */
    protected static function displayMessages(array $messages): void
    {
        foreach ($messages as $message) {
            note(sprintf(
                '<%s>%s</>%s %s',
                $message->type->style(),
                str_pad(isset($message->code) ? (string) $message->code : $message->type->value, 7, ' ', STR_PAD_BOTH),
                isset($message->source->value) ? " [{$message->source->value}]" : '',
                $message->message,
            ));
        }
    }

    /**
     * @param  list<Result\FlightGroup>  $flightGroups
     */
    protected function displayFlights(array $flightGroups): int
    {
        $rows = static::collectData($flightGroups);

        return windows_os() || $this->option('table')
            ? static::displayTable($rows)
            : static::displaySelect($rows);
    }

    /**
     * @param  list<Result\FlightGroup>  $flightGroups
     * @return Collection<int, list<string>>
     */
    protected static function collectData(array $flightGroups): Collection
    {
        return collect($flightGroups)->map(self::extractRow(...));
    }

    /**
     * @return list<string>
     */
    private static function extractRow(Result\FlightGroup $flightGroup): array
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

        return -1 + (int) text(
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
        return -1 + (int) select(
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
