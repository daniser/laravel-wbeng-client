<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Stores;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Enumerable;
use TTBooking\WBEngine\Contracts\StorableState;

class FilesystemStorage extends StateStorage
{
    public function __construct(Container $container, protected string $path = 'wbeng/state')
    {
        parent::__construct($container);
    }

    public function has(string $id): bool
    {
        // TODO: Implement has() method.
    }

    public function get(string $id): StorableState
    {
        // TODO: Implement get() method.
    }

    public function put(StorableState $state): StorableState
    {
        // TODO: Implement put() method.
    }

    protected function getSessionHistory(string $id): Enumerable
    {
        // TODO: Implement getSessionHistory() method.
    }
}
