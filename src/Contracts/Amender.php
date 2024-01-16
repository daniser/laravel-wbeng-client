<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Contracts;

interface Amender
{
    public function amend(object $item, string $path, string $key): void;
}
