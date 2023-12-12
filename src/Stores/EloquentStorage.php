<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Stores;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Enumerable;
use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\Contracts\StorableState;
use TTBooking\WBEngine\Exceptions\SessionNotFoundException;
use TTBooking\WBEngine\Exceptions\StateNotFoundException;
use TTBooking\WBEngine\Models\State;
use TTBooking\WBEngine\QueryInterface;
use TTBooking\WBEngine\ResultInterface;

class EloquentStorage implements StateStorage
{
    /** @var State<ResultInterface> */
    protected State $model;

    /**
     * @param  State<ResultInterface>|class-string<State<ResultInterface>>  $model
     */
    public function __construct(State|string $model = State::class)
    {
        $this->model = is_string($model) ? new $model : $model;
    }

    public function has(string $id): bool
    {
        return $this->model->newQuery()->whereKey($id)->exists();
    }

    /**
     * @return State<ResultInterface>
     *
     * @throws StateNotFoundException
     */
    public function get(string $id): State
    {
        try {
            return $this->model->newQuery()->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new StateNotFoundException("State [$id] not found", $e->getCode(), $e);
        }
    }

    /**
     * @param  StorableState<ResultInterface>  $state
     * @return State<ResultInterface>
     */
    public function put(StorableState $state): State
    {
        if ($state instanceof State) {
            $state->save();

            return $state;
        }

        return $this->model->newQuery()->forceCreate([
            'base_uri' => $state->getBaseUri(),
            'query' => $state->getQuery(),
            'result' => $state->getResult(),
        ]);
    }

    public function hasSession(string $id): bool
    {
        return $this->model->newQuery()->where('session_uuid', $id)->exists();
    }

    /**
     * @param  class-string<QueryInterface<ResultInterface>>|null  $queryType
     * @return Enumerable<string, StorableState<ResultInterface>>
     *
     * @throws SessionNotFoundException
     */
    public function session(string $id, ?string $queryType = null): Enumerable
    {
        return $this->model->newQuery() // @phpstan-ignore-line
            ->where('session_uuid', $id)
            ->when(isset($queryType))
            ->where('endpoint', $queryType::getEndpoint()) // @phpstan-ignore-line
            ->get()
            ->keyBy('uuid');
    }
}
