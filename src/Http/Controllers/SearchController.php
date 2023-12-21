<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use TTBooking\WBEngine\ClientInterface;
use TTBooking\WBEngine\Http\Requests\SearchRequest;

use function TTBooking\WBEngine\Functional\do\fly;

class SearchController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(SearchRequest $request, ClientInterface $client): JsonResponse
    {
        $state = $client->query(
            fly()->complex()
        );

        return new JsonResponse($state);
    }
}
