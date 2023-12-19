<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use Http\Promise\Promise;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use TTBooking\WBEngine\DTO\Common\Query\Context;
use TTBooking\WBEngine\DTO\Enums\RespondType;

/**
 * @extends Support\Manager<ClientInterface>
 */
class ConnectionManager extends Support\Manager implements ClientInterface, Contracts\ClientFactory
{
    protected string $selectorKey = 'wbeng-client.connection';

    public function continue(?StateInterface $state = null): ClientInterface
    {
        return $this->connection()->continue($state);
    }

    public function query(QueryInterface $query): StateInterface
    {
        return $this->connection()->query($query);
    }

    public function asyncQuery(QueryInterface $query): Promise
    {
        return $this->connection()->asyncQuery($query);
    }

    /**
     * @param array{
     *     uri: string,
     *     login: string,
     *     password: string,
     *     provider: string,
     *     salePoint: string|null,
     *     currency: string,
     *     locale: string,
     *     respondType: RespondType,
     *     id: int,
     *     context_id: int|null,
     *     legacy: bool,
     * } $config
     *
     * @throws BindingResolutionException
     */
    protected function createDefaultDriver(array $config): ClientInterface
    {
        /** @var bool $legacy */
        $legacy = Arr::pull($config, 'legacy');

        return $this->container->make(ClientInterface::class, [
            'baseUri' => Arr::pull($config, 'uri'),
            'context' => new Context(...$config),
            'legacy' => $legacy,
        ]);
    }
}
