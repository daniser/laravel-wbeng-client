<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Stores;

use Illuminate\Contracts\Container\Container;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Str;
use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\Exceptions\StateNotFoundException;
use TTBooking\WBEngine\ResultInterface;
use TTBooking\WBEngine\SerializerInterface;
use TTBooking\WBEngine\StorableState;

class DatabaseStorage implements StateStorage
{
    public function __construct(
        protected Container $container,
        protected SerializerInterface $serializer,
        protected ConnectionInterface $connection,
        protected string $table = 'wbeng_state',
    ) {
    }

    public function has(string $id): bool
    {
        return $this->connection->table($this->table)->where('uuid', $id)->exists();
    }

    public function get(string $id): StorableState
    {
        $record = $this->connection->table($this->table)->where('uuid', $id)->first();

        if (! $record) {
            throw new StateNotFoundException("State [$id] not found");
        }

        $record = (array) $record;

        /** @var StorableState<ResultInterface> $state */
        $state = $this->container->make(StorableState::class);

        return $state
            ->id($record['uuid'])
            ->baseUri($record['base_uri'])
            ->query($record['query'])
            ->result($record['result']);
    }

    public function put(StorableState $state): StorableState
    {
        $this->connection->table($this->table)->insert([
            'uuid' => $id = $state->id ?? (string) Str::orderedUuid(),
            'base_uri' => $state->baseUri,
            'endpoint' => $state->query::getEndpoint(),
            'query' => $this->serializer->serialize($state->query),
            'result' => $this->serializer->serialize($state->result),
        ]);

        return $state->id($id);
    }
}
