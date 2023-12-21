<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Contracts;

interface SessionFactory
{
    public function session(string $id, ?string $connection = null): Session;
}
