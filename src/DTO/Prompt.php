<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\DTO;

use Stringable;

class Prompt implements Stringable
{
    public function __construct(
        public string|int $value,

        public string $title,

        public ?string $subtitle = null,

        public ?string $icon = null,

        public ?int $level = null,
    ) {
    }

    public function __toString(): string
    {
        return $this->title;
    }
}
