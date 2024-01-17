<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Contracts;

/**
 * @template-contravariant TItem of object
 */
interface Amender
{
    /**
     * @phpstan-param TItem $item
     */
    public function amend(object $item, string|int $key, object $entity, string $path): void;
}
