<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

use TTBooking\WBEngine\Facades\WBEngine;

function wbeng(string $connection = null): ClientInterface
{
    return WBEngine::connection($connection);
}
