<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Stores;

use Illuminate\Support\Enumerable;
use Illuminate\Support\Str;
use TTBooking\WBEngine\Contracts\StorableState;
use TTBooking\WBEngine\Exceptions\StateNotFoundException;
use TTBooking\WBEngine\ResultInterface;

class ArrayStorage extends StateStorage
{
    /** @var array<string, StorableState<ResultInterface>> */
    protected array $states = [];

    /** @var array<string, array<string, StorableState<ResultInterface>>> */
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

    protected function getSessionHistory(string $id): Enumerable
    {
        return collect($this->sessions[$id]);
    }
}
