<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use Exception;
use Http\Promise\Promise;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Psr\Http\Client\ClientExceptionInterface;
use TTBooking\WBEngine\Contracts\StorableState;
use TTBooking\WBEngine\DTO\Common\Query\Context;
use TTBooking\WBEngine\DTO\Enums\RespondType;

/**
 * @extends Support\Manager<ClientInterface<StorableState>>
 *
 * @implements ClientInterface<StorableState>
 */
class ConnectionManager extends Support\Manager implements ClientInterface, Contracts\ClientFactory
{
    protected string $selectorKey = 'wbeng-client.connection';

    public function continue(?StateInterface $state = null): ClientInterface
    {
        return $this->connection()->continue($state);
    }

    /**
     * @template TResult of ResultInterface
     * @template TQuery of QueryInterface<TResult>
     *
     * @phpstan-param TQuery $query
     *
     * @return StorableState<TResult, TQuery>
     *
     * @throws ClientExceptionInterface
     */
    public function query(QueryInterface $query): StorableState
    {
        /** @var StorableState<TResult, TQuery> */
        return $this->connection()->query($query);
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
     * @return ClientInterface<StorableState<ResultInterface, QueryInterface<ResultInterface>>>
     *
     * @throws BindingResolutionException
     */
    protected function createDefaultDriver(array $config): ClientInterface
    {
        /** @var bool $legacy */
        $legacy = Arr::pull($config, 'legacy');

        return $this->container->make(Client::class, [
            'baseUri' => Arr::pull($config, 'uri'),
            'defaultContext' => new Context(...$config),
            'defaultAttributes' => [
                StateInterface::ATTR_LEGACY => $legacy,
            ],
        ]);
    }
}
