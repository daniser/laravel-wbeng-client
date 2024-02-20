<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Contracts;

use TTBooking\WBEngine\DTO\Prompt;

interface Prompter
{
    /**
     * @return list<Prompt>
     */
    public function prompt(string $input): array;
}
