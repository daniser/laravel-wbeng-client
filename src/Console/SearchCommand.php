<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Console;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use TTBooking\WBEngine\ClientInterface;
use TTBooking\WBEngine\Contracts\ClientFactory;
use TTBooking\WBEngine\Contracts\StorableState;
use TTBooking\WBEngine\DTO\Common\Result;
use TTBooking\WBEngine\DTO\CreateBooking\Result as CBResult;
use TTBooking\WBEngine\DTO\Enums\Gender;
use TTBooking\WBEngine\DTO\Enums\PassengerType;
use TTBooking\WBEngine\DTO\Prompt;
use TTBooking\WBEngine\DTO\SearchFlights\Query as SearchQuery;
use TTBooking\WBEngine\DTO\SelectFlight\Query as SelectQuery;
use TTBooking\WBEngine\StateInterface;

use function Laravel\Prompts\{note, search, select, spin, table, text, warning};
use function TTBooking\WBEngine\Functional\a\passenger;
use function TTBooking\WBEngine\Functional\do\{book, choose, fly};
use function TTBooking\WBEngine\{data_get, trans_enum_cases};

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
        /** @var Closure(string): array<string, string> $prompter */
        $prompter = static function (string $input) {
            $prompts = app('wbeng-client.prompters.airport')->prompt($input);

            /** @return array<string, string> */
            return collect($prompts)
                ->mapWithKeys(static fn (Prompt $prompt) => [(string) $prompt->value => $prompt->title])
                ->all();
        };

        /** @var string $connection */
        $connection = $this->option('connection');

        $client = $clientFactory->connection($connection);

        $searchState = $this->searchFlights(
            client: $client,
            origin: $this->getDepartureLocation($prompter),
            destination: $this->getArrivalLocation($prompter),
            date: $this->getDepartureDate(),
        );

        $searchResult = $searchState->getResult();

        static::displayStatus($searchResult->context);
        static::displayMessages($searchResult->messages);

        if (! $searchResult->flightGroups) {
            warning('No flights found.');

            return static::FAILURE;
        }

        $flightGroupId = $this->displayFlights($searchResult->flightGroups);

        $selectState = $this->selectFlight(
            client: $client->continue($searchState),
            searchResult: $searchResult,
            flightGroupId: $flightGroupId,
            itineraryId: 0,
            flightId: 0,
        );

        $selectResult = $selectState->getResult();

        static::displayStatus($selectResult->context);
        static::displayMessages($selectResult->messages);

        if (! $selectResult->flightGroups) {
            warning('No flights found.');

            return static::FAILURE;
        }

        $this->displayFlights($selectResult->flightGroups);

        $bookResult = $this->createBooking(
            clientFactory: $clientFactory,
            searchResult: $searchResult,
            flightGroupId: $flightGroupId,
            itineraryId: 0,
            flightId: 0,
        );

        $bookResult->context && static::displayStatus($bookResult->context);
        static::displayMessages($bookResult->messages);

        return static::SUCCESS;
    }

    /**
     * @param  null|callable(string): array<string, string>  $prompter
     */
    protected function getDepartureLocation(?callable $prompter = null): string
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
    protected function getArrivalLocation(?callable $prompter = null): string
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
    protected static function getLocation(string $label, ?callable $prompter = null, string $placeholder = '', string $hint = ''): string
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

    /**
     * @template TState of StorableState<Result, SearchQuery>
     *
     * @param  ClientInterface<TState>  $client
     *
     * @phpstan-return TState
     */
    protected function searchFlights(ClientInterface $client, string $origin, string $destination, string $date): StateInterface
    {
        return spin(fn (): StateInterface => $client->query(
            fly()->from($origin)->to($destination)->on($date)//->sortByPrice()
        ), 'Searching flights...');
    }

    /**
     * @template TState of StorableState<Result, SelectQuery>
     *
     * @param  ClientInterface<TState>  $client
     *
     * @phpstan-return TState
     */
    protected function selectFlight(ClientInterface $client, Result $searchResult, int $flightGroupId, int $itineraryId, int $flightId): StateInterface
    {
        return spin(fn (): StateInterface => $client->query(
            choose()->fromSearchResult($searchResult, $flightGroupId, $itineraryId, $flightId)
        ), 'Checking availability...');
    }

    protected static function getCustomerName(): string
    {
        return text(
            label: 'Customer name',
            placeholder: 'Ivanov Ivan',
            required: true,
        );
    }

    protected static function getCustomerEmail(): string
    {
        return text(
            label: 'Customer e-mail',
            placeholder: 'i.ivanov@example.com',
            required: true,
        );
    }

    protected static function getCustomerPhone(): string
    {
        return text(
            label: 'Customer phone',
            placeholder: '+79001234567',
            required: true,
        );
    }

    protected static function choosePassengerType(): PassengerType
    {
        $type = (string) select(
            label: 'Passenger type',
            options: trans_enum_cases(PassengerType::class),
            default: PassengerType::Adult->name,
        );

        /** @var PassengerType */
        return constant(PassengerType::class.'::'.$type);
    }

    protected static function choosePassengerGender(): Gender
    {
        $type = (string) select(
            label: 'Passenger gender',
            options: trans_enum_cases(Gender::class),
        );

        /** @var Gender */
        return constant(Gender::class.'::'.$type);
    }

    protected static function getPassengerSurname(): string
    {
        return text(
            label: 'Passenger last name',
            placeholder: 'Ivanov',
            required: true,
        );
    }

    protected static function getPassengerName(): string
    {
        return text(
            label: 'Passenger first name',
            placeholder: 'Ivan',
            required: true,
        );
    }

    protected static function getPassengerMiddleName(): string
    {
        return text(
            label: 'Passenger middle name',
            placeholder: 'Ivanovich',
        );
    }

    protected static function getPassengerBirthDate(): string
    {
        return text(
            label: 'Passenger birth date',
            required: true,
        );
    }

    protected static function getPassengerPhone(): string
    {
        return text(
            label: 'Passenger phone',
            placeholder: '+79001234567',
            required: true,
        );
    }

    protected function createBooking(ClientFactory $clientFactory, Result $searchResult, int $flightGroupId, int $itineraryId, int $flightId): CBResult
    {
        /** @var string $connection */
        $connection = $this->option('connection');

        $query = book()
            ->fromSearchResult($searchResult, $flightGroupId, $itineraryId, $flightId)
            ->customer(static::getCustomerName(), static::getCustomerEmail(), static::getCustomerPhone())
            ->passengers(passenger()
                ->type(static::choosePassengerType())
                ->gender(static::choosePassengerGender())
                ->lastName(static::getPassengerSurname())
                ->firstName(static::getPassengerName())
                ->middleName(static::getPassengerMiddleName())
                ->birthDate(static::getPassengerBirthDate())
                ->phone(static::getPassengerPhone())
            );

        return spin(fn (): CBResult => $clientFactory->connection($connection)->query($query)->getResult(), 'Booking flight...');
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
