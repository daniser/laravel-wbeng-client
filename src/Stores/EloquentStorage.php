<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Stores;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\LazyCollection;
use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\Contracts\StorableState;
use TTBooking\WBEngine\Exceptions\StateNotFoundException;
use TTBooking\WBEngine\Exceptions\UnsupportedConditionException;
use TTBooking\WBEngine\Models\State;
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

    /**
     * @return LazyCollection<string, State<ResultInterface>>
     */
    public function where(array $conditions): LazyCollection
    {
        foreach ($conditions as $attr => $value) {
            if ($attr !== 'sessionId') {
                throw new UnsupportedConditionException("Attribute [$attr] not supported in condition");
            }

            return $this->model->newQuery()->where('session_uuid', $value)->lazyById()->keyBy('uuid');
        }

        return LazyCollection::empty();
    }

    /**
     * @return LazyCollection<string, State<ResultInterface>>
     */
    public function all(): LazyCollection
    {
        return $this->model->newQuery()->lazyById()->keyBy('uuid');
    }
}
