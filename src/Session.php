<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use Illuminate\Support\Enumerable;
use TTBooking\WBEngine\Contracts\StorableState;

class Session implements ClientInterface
{
    /**
     * @param  Enumerable<string, StorableState<ResultInterface>>  $history
     */
    public function __construct(protected Enumerable $history, protected Client $client)
    {
        $this->client = $client->continue($history->first());
    }

    public function query(QueryInterface $query): StateInterface
    {
        return $this->client->query($query);
    }

    /**
     * @return Enumerable<string, StorableState<ResultInterface>>
     */
    public function history(): Enumerable
    {
        return $this->history;
    }
}
