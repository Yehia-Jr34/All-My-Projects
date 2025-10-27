<?php

namespace App\Http\Controllers\API\V1\Video;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\Api\Video\AddVideoRequest;
use App\Http\Requests\Api\Video\EditVideoRequest;
use App\Services\Video\VideoService;
use Illuminate\Http\JsonResponse;

class VideoController extends BaseApiController
{
    public function __construct(
        private VideoService $videoService
    )
    {
    }

    public function create(AddVideoRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->videoService->create($data);
        return $this->sendSuccess('video added successfully', [], StatusCodeEnum::CREATED->value);
    }

    public function update(EditVideoRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->videoService->edit($data);
        return $this->sendSuccess('video updated successfully', $response, StatusCodeEnum::OK->value);
    }

    public function getTitles(int $training_id): JsonResponse
    {
        $date = $this->videoService->getTitles($training_id);
        return $this->sendSuccess('video titles found', $date, StatusCodeEnum::OK->value);
    }
}
