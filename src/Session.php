<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use Http\Promise\Promise;
use Illuminate\Support\Enumerable;
use TTBooking\WBEngine\Contracts\StorableState;

class Session implements ClientInterface
{
    /**
     * @param  Enumerable<string, StorableState<ResultInterface>>  $history
     */
    public function __construct(protected Enumerable $history, protected ClientInterface $client)
    {
        $this->client = $client->continue($history->first());
    }

    public function continue(?StateInterface $state = null): self
    {
        return new self($this->history, $this->client->continue($state));
    }

    public function query(QueryInterface $query): StateInterface
    {
        return $this->client->query($query);
    }

    public function asyncQuery(QueryInterface $query): Promise
    {
        return $this->client->asyncQuery($query);
    }

    /**
     * @return Enumerable<string, StorableState<ResultInterface>>
     */
    public function history(): Enumerable
    {
        return $this->history;
    }
}
