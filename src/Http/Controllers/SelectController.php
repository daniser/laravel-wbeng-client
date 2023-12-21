<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use TTBooking\WBEngine\Http\Requests\SelectRequest;
use TTBooking\WBEngine\Session;

use function TTBooking\WBEngine\Functional\do\choose;

class SelectController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(SelectRequest $request, Session $session): JsonResponse
    {
        $result = $session->history()->first()?->getResult();

        $state = $result ?? $session->query(
            choose()->fromSearchResult($result, 0, 0, 0)
        );

        return new JsonResponse($state);
    }
}
