<?php

namespace App\Http\Controllers\API\V1\InternalTrainer;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\InternalTrainer\StoreInternalTrainerRequest;
use App\Http\Resources\InternalTrainer\ProviderInternalTrainersResource;
use App\Services\InternalTrainer\InternalTrainerService;
use Illuminate\Http\JsonResponse;

class InternalTrainerController extends BaseApiController
{
    public function __construct(
        private InternalTrainerService $internalTrainerService
    )
    {
    }

    public function create(StoreInternalTrainerRequest $request): JsonResponse
    {
        $data = $request->validated();

        $this->internalTrainerService->create($data);

        return $this->sendSuccess('Internal Trainer Created successfully.',[], StatusCodeEnum::CREATED->value);
    }

    public function index():JsonResponse{

        $data = $this->internalTrainerService->indexByProvider();

        return $this->sendSuccess('Internal Trainer fetched successfully.',ProviderInternalTrainersResource::collection($data), StatusCodeEnum::CREATED->value);
    }

}
