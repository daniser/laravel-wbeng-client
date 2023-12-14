<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Contracts;

use TTBooking\WBEngine\Client;

interface SessionFactory
{
    public function session(string $id, ?string $connection = null): Client;
}
