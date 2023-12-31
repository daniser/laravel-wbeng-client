<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use Illuminate\Support\Enumerable;
use TTBooking\WBEngine\Contracts\ClientFactory;
use TTBooking\WBEngine\Contracts\SessionFactory;
use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\Contracts\StorableState;
use TTBooking\WBEngine\Exceptions\UnsupportedConditionException;

/**
 * @template TStateStorage of StateStorage
 */
class ExtendedStorage implements SessionFactory, StateStorage
{
    /**
     * @phpstan-param TStateStorage $storage
     */
    public function __construct(protected StateStorage $storage, protected ClientFactory $clientFactory)
    {
    }

    public function has(string $id): bool
    {
        return $this->storage->has($id);
    }

    public function get(string $id): StorableState
    {
        return $this->storage->get($id);
    }

    public function put(StorableState $state): StorableState
    {
        return $this->storage->put($state);
    }

    public function where(array $conditions): Enumerable
    {
        try {
            return $this->storage->where($conditions);
        } catch (UnsupportedConditionException $e) {
            throw $e;
        }
    }

    public function all(): Enumerable
    {
        return $this->storage->all();
    }

    public function session(string $id, ?string $connection = null): Session
    {
        return new Session(
            $this->where([StorableState::ATTR_SESSION_ID => $id]),
            $this->clientFactory->connection($connection)
        );
    }
}
