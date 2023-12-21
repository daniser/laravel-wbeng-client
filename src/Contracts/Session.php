<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Contracts;

use Illuminate\Support\Enumerable;
use TTBooking\WBEngine\ClientInterface;
use TTBooking\WBEngine\ResultInterface;

interface Session extends ClientInterface
{
    /**
     * @return Enumerable<string, StorableState<ResultInterface>>
     */
    public function history(): Enumerable;
}
