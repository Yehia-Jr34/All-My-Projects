<?php

namespace App\Http\Controllers\API\V1\Search;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Search\SearchResource;
use App\Services\Search\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends BaseApiController
{
    public function __construct(
        private SearchService $searchService
    )
    {
    }

    public function search(Request $request): JsonResponse
    {
        $response = $this->searchService->search($request->search);
        return $this->sendSuccess('Success search', SearchResource::make($response), StatusCodeEnum::OK->value);
    }
}
