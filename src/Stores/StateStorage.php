<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Stores;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Enumerable;
use TTBooking\WBEngine\Contracts\StateStorage as StateStorageContract;
use TTBooking\WBEngine\Contracts\StorableState;
use TTBooking\WBEngine\ResultInterface;
use TTBooking\WBEngine\Session;

abstract class StateStorage implements StateStorageContract
{
    public function __construct(protected Container $container)
    {
    }

    public function session(string $id): Session
    {
        //return new Session($this->container, $id, $this->getSessionHistory($id));

        return $this->container->make(Session::class, [
            'id' => $id,
            'history' => $this->getSessionHistory($id),
        ]);
    }

    /**
     * @return Enumerable<string, StorableState<ResultInterface>>
     */
    abstract protected function getSessionHistory(string $id): Enumerable;
}
