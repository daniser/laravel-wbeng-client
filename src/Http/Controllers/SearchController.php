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
use TTBooking\WBEngine\SerializerInterface;
use TTBooking\WBEngine\Session;

use function TTBooking\WBEngine\Functional\do\fly;

class SearchController extends Controller
{
    public function __construct(protected SerializerInterface $serializer)
    {
    }

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

        return new JsonResponse($this->serializer->serialize($result), json: true);
    }

    public function load(Session $session): JsonResponse
    {
        $result = $session->history('flights')->firstOrFail()->getResult();

        return new JsonResponse($this->serializer->serialize($result), json: true);
    }
}
