<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Middleware;

use Closure;
use Illuminate\Contracts\Container\Container;
use ParentIterator;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use TTBooking\WBEngine\Contracts\Amender;
use TTBooking\WBEngine\QueryInterface;
use TTBooking\WBEngine\ResultInterface;
use TTBooking\WBEngine\StateInterface;
use TTBooking\WBEngine\Support\RecursivePathIterator;

class AmendMiddleware
{
    /**
     * @param  array<class-string, list<class-string<Amender>>>  $typeAmenders
     * @param  array<string, list<class-string<Amender>>>  $pathAmenders
     */
    public function __construct(
        protected Container $container,
        protected array $typeAmenders = [],
        protected array $pathAmenders = [],
    ) {
    }

    /**
     * @template TResult of ResultInterface
     * @template TQuery of QueryInterface<TResult>
     *
     * @phpstan-param  TQuery $query
     *
     * @param  Closure(TQuery): StateInterface<TResult, TQuery>  $next
     * @return StateInterface<TResult, TQuery>
     */
    public function handle(QueryInterface $query, Closure $next): StateInterface
    {
        $state = $next($query);
        $result = $state->getResult();

        /** @var RecursivePathIterator<array-key, array<mixed>|object> $iterator */
        $iterator = new RecursiveIteratorIterator(
            new ParentIterator(new RecursivePathIterator(new RecursiveArrayIterator($result))), // @phpstan-ignore-line
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $key => $item) {
            $path = $iterator->path();

            if (is_object($item)) {
                $this->amend($this->typeAmenders[$item::class], $item, $path, $key);

                foreach ($this->pathAmenders as $pattern => $amenderClasses) {
                    if (fnmatch($pattern, $path)) {
                        $this->amend($amenderClasses, $item, $path, $key);
                    }
                }
            }
        }

        return $state->setResult($result);
    }

    /**
     * @param  list<class-string<Amender>>  $amenderClasses
     */
    protected function amend(array $amenderClasses, object $item, string $path, string $key): void
    {
        foreach ($amenderClasses as $amenderClass) {
            /** @var Amender $amender */
            $amender = $this->container->make($amenderClass);
            $amender->amend($item, $path, $key);
        }
    }
}
