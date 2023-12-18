<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use Illuminate\Contracts\Pipeline\Pipeline;

class ExtendedClient implements ClientInterface
{
    /**
     * @param  list<class-string>  $middleware
     */
    public function __construct(
        protected ClientInterface $client,
        protected Pipeline $pipeline,
        protected array $middleware = [],
    ) {
    }

    public function query(QueryInterface $query): StateInterface
    {
        return $this->pipeline
            ->send($query)
            ->through($this->middleware)
            ->then($this->client->query(...));
    }

    /**
     * @param  list<mixed>  $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->client->$name(...$arguments);
    }
}
