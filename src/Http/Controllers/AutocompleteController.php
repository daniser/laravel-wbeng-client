<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use TTBooking\WBEngine\Contracts\Prompter;

class AutocompleteController extends Controller
{
    public function airports(Prompter $airportPrompter, string $input): JsonResponse
    {
        return new JsonResponse($airportPrompter->prompt($input));
    }

    public function airlines(Prompter $airlinePrompter, string $input): JsonResponse
    {
        return new JsonResponse($airlinePrompter->prompt($input));
    }
}
