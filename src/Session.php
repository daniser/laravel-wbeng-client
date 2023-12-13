<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use TTBooking\WBEngine\Contracts\StorableState;

class Session implements ClientInterface
{
    protected ClientInterface $client;

    /**
     * @param  Enumerable<string, StorableState<ResultInterface>>  $history
     */
    public function __construct(
        protected Container $container,
        protected ?string $id = null,
        protected Enumerable $history = new Collection,
    ) {
        $initialState = $this->history->first();
        $this->id ??= $initialState?->getSessionId();

        $this->client = $this->container->make(Client::class, [
            'baseUri' => $initialState?->getBaseUri(),
            'context' => $initialState?->getQuery()->getContext(),
        ]);
    }

    /**
     * @return StorableState<ResultInterface>
     */
    public function query(QueryInterface $query): StorableState
    {
        $state = $this->client->query($query);

        return $this->id ? $state->setSessionId($this->id) : $state;
    }

    /**
     * @param  class-string<QueryInterface<ResultInterface>>|null  $queryType
     * @return Enumerable<string, StorableState<ResultInterface>>
     */
    public function history(?string $queryType = null): Enumerable
    {
        return isset($queryType)
            ? $this->history->filter(static fn (StorableState $state) => $state->getQuery() instanceof $queryType)
            : $this->history;
    }
}
