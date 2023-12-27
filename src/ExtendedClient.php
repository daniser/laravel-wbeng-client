<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use Exception;
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

    /**
     * @template TResult of ResultInterface
     * @template TQuery of QueryInterface<TResult>
     *
     * @phpstan-param TQuery $query
     *
     * @return StorableState<TResult, TQuery>
     *
     * @throws ClientException
     */
    public function query(QueryInterface $query): StorableState
    {
        /** @var StorableState<TResult, TQuery> */
        return $this->pipeline
            ->send($query)
            ->through($this->middleware)
            ->then($this->client->query(...));
    }

    /**
     * @template TResult of ResultInterface
     * @template TQuery of QueryInterface<TResult>
     *
     * @phpstan-param TQuery $query
     *
     * @return Promise<StorableState<TResult, TQuery>>
     *
     * @throws Exception
     */
    public function asyncQuery(QueryInterface $query): Promise
    {
        /** @var Promise<StorableState<TResult, TQuery>> */
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
