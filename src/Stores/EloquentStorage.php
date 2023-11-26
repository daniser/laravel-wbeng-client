<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Stores;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use TTBooking\WBEngine\Contracts\StateStorage;
use TTBooking\WBEngine\Exceptions\StateNotFoundException;
use TTBooking\WBEngine\Models\State as StateModel;
use TTBooking\WBEngine\StorableState;

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

    public function has(string $id): bool
    {
        return $this->model->newQuery()->whereKey($id)->exists();
    }

    public function get(string $id): StorableState
    {
        try {
            /** @var StateModel $stateModel */
            $stateModel = $this->model->newQuery()->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new StateNotFoundException("State [$id] not found", $e->getCode(), $e);
        }

        try {
            $state = $this->container?->make(StorableState::class) ?? new StorableState;
        } catch (BindingResolutionException) {
            $state = new StorableState;
        }

        return $state
            ->id($stateModel->uuid)
            ->baseUri($stateModel->base_uri)
            ->query($stateModel->query)
            ->result($stateModel->result);
    }

    public function put(StorableState $state): StorableState
    {
        /** @var string $id */
        $id = $this->model->newQuery()->forceCreate([
            'base_uri' => $state->baseUri,
            'query' => $state->query,
            'result' => $state->result,
        ])->getKey();

        return $state->id($id);
    }
}
