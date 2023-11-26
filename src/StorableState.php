<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

/**
 * @template TResult of ResultInterface
 *
 * @extends State<TResult>
 */
class StorableState extends State
{
    public ?string $id = null;

    public ?string $parentId = null;

    /**
     * @return $this
     */
    public function id(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return $this
     */
    public function parentId(string $parentId): static
    {
        $this->parentId = $parentId;

        return $this;
    }
}
