<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Stores;

use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Enumerable;
use TTBooking\WBEngine\Contracts\StorableState;
use TTBooking\WBEngine\Exceptions\StateNotFoundException;
use TTBooking\WBEngine\Models\State;
use TTBooking\WBEngine\ResultInterface;

class EloquentStorage extends StateStorage
{
    /** @var State<ResultInterface> */
    protected State $model;

    /**
     * @param  State<ResultInterface>|class-string<State<ResultInterface>>  $model
     */
    public function __construct(Container $container, State|string $model = State::class)
    {
        parent::__construct($container);

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

    protected function getSessionHistory(string $id): Enumerable
    {
        return $this->model->newQuery()->where('session_uuid', $id)->get()->keyBy('uuid');
    }
}
