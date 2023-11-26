<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Stores;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\Exceptions\StateNotFoundException;
use TTBooking\WBEngine\Models\State as StateModel;
use TTBooking\WBEngine\State;

class EloquentStorage implements StateStorage
{
    protected StateModel $model;

    /**
     * @param  StateModel|class-string<StateModel>  $model
     */
    public function __construct(protected ?Container $container = null, StateModel|string $model = StateModel::class)
    {
        $this->model = is_string($model) ? new $model : $model;
    }

    public function store(State $state, State $parentState = null): string
    {
        /** @var string */
        return $this->model->newQuery()->forceCreate([
            'base_uri' => $state->baseUri,
            'query' => $state->query,
            'result' => $state->result,
        ])->getKey();
    }

    public function retrieve(string $id): State
    {
        try {
            /** @var StateModel $stateModel */
            $stateModel = $this->model->newQuery()->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new StateNotFoundException("State [$id] not found", $e->getCode(), $e);
        }

        try {
            $state = $this->container?->make(State::class) ?? new State;
        } catch (BindingResolutionException) {
            $state = new State;
        }

        return $state
            ->setBaseUri($stateModel->base_uri)
            ->setQuery($stateModel->query)
            ->setResult($stateModel->result);
    }
}
