<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Stores;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\Contracts\StorableState;
use TTBooking\WBEngine\Exceptions\StateNotFoundException;
use TTBooking\WBEngine\QueryInterface;
use TTBooking\WBEngine\ResultInterface;

class ArrayStorage implements StateStorage
{
    /** @var array<string, StorableState<ResultInterface, QueryInterface<ResultInterface>>> */
    protected array $states = [];

    /** @var array<string, array<string, StorableState<ResultInterface, QueryInterface<ResultInterface>>>> */
    protected array $sessions = [];

    public function has(string $id): bool
    {
        return isset($this->states[$id]);
    }

    public function get(string $id): StorableState
    {
        return $this->states[$id] ?? throw new StateNotFoundException("State [$id] not found");
    }

    public function put(StorableState $state): StorableState
    {
        $id = $state->getId() ?? (string) Str::orderedUuid();
        $sessionId = $state->getSessionId() ?? $id;

        return $this->states[$id] = $this->sessions[$sessionId][$id] = $state->setId($id)->setSessionId($sessionId);
    }

    /**
     * @return Collection<string, StorableState<ResultInterface, QueryInterface<ResultInterface>>>
     */
    public function where(array $conditions): Collection
    {
        /** @var Collection<string, StorableState<ResultInterface, QueryInterface<ResultInterface>>> $states */
        $states = collect(array_key_exists(StorableState::ATTR_SESSION_ID, $conditions)
            ? $this->sessions[$conditions[StorableState::ATTR_SESSION_ID]] ?? []
            : $this->states
        );

        unset($conditions[StorableState::ATTR_SESSION_ID]);

        foreach ($conditions as $attribute => $value) {
            $states = $states->filter(static fn (StorableState $state) => $state->getAttr($attribute) === $value);
        }

        return $states;
    }

    /**
     * @return Collection<string, StorableState<ResultInterface, QueryInterface<ResultInterface>>>
     */
    public function all(): Collection
    {
        return collect($this->states);
    }
}
