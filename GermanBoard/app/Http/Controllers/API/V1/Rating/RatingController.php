<?php

namespace App\Http\Controllers\API\V1\Rating;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\Api\Ratings\AddRatingRequest;
use App\Services\Rating\RatingService;
use Illuminate\Http\JsonResponse;

class RatingController extends BaseApiController
{
    public function __construct(
        private readonly RatingService $ratingService
    ) {}

    public function store(AddRatingRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->ratingService->store($data);
        return $this->sendSuccess('Rating created successfully', [], StatusCodeEnum::CREATED->value);
    }
}
