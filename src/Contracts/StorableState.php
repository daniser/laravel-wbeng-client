<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Contracts;

use TTBooking\WBEngine\ResultInterface;
use TTBooking\WBEngine\StateInterface;

/**
 * @template TResult of ResultInterface
 *
 * @extends StateInterface<TResult>
 */
interface StorableState extends StateInterface
{
    public const ATTR_SESSION_ID = 'session_id';

    /**
     * @return $this
     */
    public function setId(string $id): static;

    public function getId(): ?string;

    /**
     * @return $this
     */
    public function setSessionId(string $sessionId): static;

    public function getSessionId(): ?string;
}
