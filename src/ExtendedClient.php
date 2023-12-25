<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use Http\Promise\Promise;
use Illuminate\Contracts\Pipeline\Pipeline;
use TTBooking\WBEngine\Contracts\StorableState;

/**
 * @implements ClientInterface<StorableState>
 */
class ExtendedClient implements ClientInterface
{
    /**
     * @param  ClientInterface<StorableState<ResultInterface, QueryInterface<ResultInterface>>>  $client
     * @param  list<class-string>  $middleware
     */
    public function __construct(
        protected ClientInterface $client,
        protected Pipeline $pipeline,
        protected array $middleware = [],
    ) {
    }

    public function continue(?StateInterface $state = null): self
    {
        return new self($this->client->continue($state), $this->pipeline, $this->middleware);
    }

    public function query(QueryInterface $query): StateInterface
    {
        return $this->pipeline
            ->send($query)
            ->through($this->middleware)
            ->then($this->client->query(...));
    }

    public function asyncQuery(QueryInterface $query): Promise
    {
        return $this->pipeline
            ->send($query)
            ->through($this->middleware)
            ->then($this->client->asyncQuery(...));
    }

    /**
     * @param  list<mixed>  $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->client->$name(...$arguments);
    }
}
