<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use TTBooking\WBEngine\Contracts\Prompter;

class AutocompleteController extends Controller
{
    public function airports(string $input): JsonResponse
    {
        return new JsonResponse(app('wbeng-client.prompters.airport')->prompt($input));
    }

    public function airlines(string $input): JsonResponse
    {
        return new JsonResponse(app('wbeng-client.prompters.airline')->prompt($input));
    }
}
