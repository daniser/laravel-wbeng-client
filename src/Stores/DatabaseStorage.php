<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Stores;

use Illuminate\Contracts\Container\Container;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Str;
use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\Contracts\StorableState;
use TTBooking\WBEngine\Exceptions\StateNotFoundException;
use TTBooking\WBEngine\ResultInterface;
use TTBooking\WBEngine\SerializerInterface;

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
            ->setId($record['uuid'])
            ->setSessionId($record['session_uuid'])
            ->setBaseUri($record['base_uri'])
            ->setQuery($record['query'])
            ->setResult($record['result']);
    }

    public function put(StorableState $state): StorableState
    {
        $this->connection->table($this->table)->insert([
            'uuid' => $id = $state->getId() ?? (string) Str::orderedUuid(),
            'session_uuid' => $sessionId = $state->getSessionId() ?? $id,
            'base_uri' => $state->getBaseUri(),
            'endpoint' => $state->getQuery()::getEndpoint(),
            'query' => $this->serializer->serialize($state->getQuery()),
            'result' => $this->serializer->serialize($state->getResult()),
        ]);

        return $state->setId($id)->setSessionId($sessionId);
    }

    public function where(array $conditions): Enumerable
    {
        // TODO: Implement where() method.
    }

    public function all(): Enumerable
    {
        // TODO: Implement all() method.
    }
}
