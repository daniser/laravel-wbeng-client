<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use TTBooking\WBEngine\Contracts\ClientFactory;
use function TTBooking\WBEngine\fly;

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
        {from : Origin location code}
        {to : Destination location code}
        {date : Flight date}
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
        $clientFactory->connection($this->option('connection'))
            ->searchFlights(fly()
                ->from($this->argument('from'))
                ->to($this->argument('to'))
                ->at($this->argument('date'))
            );

        $this->info('Search successfully finished.');
    }
}
