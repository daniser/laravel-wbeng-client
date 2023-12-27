<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use Exception;
use Http\Promise\Promise;
use Illuminate\Support\Enumerable;
use TTBooking\WBEngine\Contracts\Session as SessionContract;
use TTBooking\WBEngine\Contracts\StorableState;

class Session implements SessionContract
{
    /**
     * @template TState of StorableState
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

    /**
     * @template TResult of ResultInterface
     * @template TQuery of QueryInterface<TResult>
     *
     * @phpstan-param TQuery $query
     *
     * @return StorableState<TResult, TQuery>
     *
     * @throws ClientException
     */
    public function query(QueryInterface $query): StorableState
    {
        /** @var StorableState<TResult, TQuery> */
        return $this->client->query($query);
    }

    /**
     * @template TResult of ResultInterface
     * @template TQuery of QueryInterface<TResult>
     *
     * @phpstan-param TQuery $query
     *
     * @return Promise<StorableState<TResult, TQuery>>
     *
     * @throws Exception
     */
    public function asyncQuery(QueryInterface $query): Promise
    {
        /** @var Promise<StorableState<TResult, TQuery>> */
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
