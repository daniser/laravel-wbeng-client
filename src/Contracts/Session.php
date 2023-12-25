<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Contracts;

use Illuminate\Support\Enumerable;
use TTBooking\WBEngine\ClientInterface;
use TTBooking\WBEngine\QueryInterface;
use TTBooking\WBEngine\ResultInterface;

/**
 * @extends ClientInterface<StorableState>
 */
interface Session extends ClientInterface
{
    /**
     * @return Enumerable<string, StorableState<ResultInterface, QueryInterface<ResultInterface>>>
     */
    public function history(?string $type = null): Enumerable;
}
