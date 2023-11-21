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

    public function query(QueryInterface $query): ResultInterface
    {
        return $this->pipeline
            ->send($query)
            ->through($this->middleware)
            ->then($this->client->query(...));
    }
}
