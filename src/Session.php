<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use Http\Promise\Promise;
use Illuminate\Support\Enumerable;
use TTBooking\WBEngine\Contracts\Session as SessionContract;
use TTBooking\WBEngine\Contracts\StorableState;

class Session implements SessionContract
{
    /**
     * @template TState of StorableState<ResultInterface, QueryInterface<ResultInterface>>
     *
     * @param  Enumerable<string, TState>  $history
     * @param  ClientInterface<TState>  $client
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

    public function history(?string $type = null): Enumerable
    {
        return $type
            ? $this->history->filter(static function (StorableState $state) use ($type) {
                return $type === $state->getQuery()::class
                    || $type === $state->getQuery()::getResultType()
                    || $type === $state->getQuery()::getEndpoint();
            })
            : $this->history;
    }
}
