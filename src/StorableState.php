<?php

declare(strict_types=1);

namespace TTBooking\WBEngine;

/**
 * @template TResult of ResultInterface
 *
 * @extends State<TResult>
 *
 * @implements Contracts\StorableState<TResult>
 */
class StorableState extends State implements Contracts\StorableState
{
    protected ?string $id = null;

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setSessionId(string $sessionId): static
    {
        return $this->setAttr(self::ATTR_SESSION_ID, $sessionId);
    }

    public function getSessionId(): ?string
    {
        /** @var string|null */
        return $this->getAttr(self::ATTR_SESSION_ID);
    }
}
