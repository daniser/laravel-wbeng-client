<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use TTBooking\WBEngine\ClientInterface;
use TTBooking\WBEngine\Contracts\StorableState;
use TTBooking\WBEngine\Http\Requests\SearchRequest;
use TTBooking\WBEngine\QueryInterface;
use TTBooking\WBEngine\ResultInterface;

use function TTBooking\WBEngine\Functional\do\fly;

class SearchController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  ClientInterface<StorableState<ResultInterface, QueryInterface<ResultInterface>>>  $client
     */
    public function __invoke(SearchRequest $request, ClientInterface $client): JsonResponse
    {
        $result = $client->query(
            fly()->from($request->from)->to($request->to)->on($request->date)
        )->getResult();

        return new JsonResponse($result);
    }
}
