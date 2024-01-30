<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use TTBooking\WBEngine\DTO\Common\Result;
use TTBooking\WBEngine\Http\Requests\SelectRequest;
use TTBooking\WBEngine\SerializerInterface;
use TTBooking\WBEngine\Session;

use function TTBooking\WBEngine\Functional\do\choose;

class SelectController extends Controller
{
    public function __construct(protected SerializerInterface $serializer)
    {
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(SelectRequest $request, Session $session): JsonResponse
    {
        /** @var Result $searchResult */
        $searchResult = $session->history('flights')->firstOrFail()->getResult();

        $result = $session->query(
            choose()->fromSearchResult($searchResult, 0, 0, 0)
        )->getResult();

        return new JsonResponse($this->serializer->serialize($result), json: true);
    }
}
