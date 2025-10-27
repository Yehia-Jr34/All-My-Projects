<?php

namespace App\Http\Controllers\API\V1\InternalTrainer;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Resources\InternalTrainer\InternalTrainerActionsResource;
use App\Services\InternalTrainer\InternalTrainerService;
use Illuminate\Http\JsonResponse;

class InternalTrainerActionController extends BaseApiController
{
    public function __construct(
        private InternalTrainerService $internalTrainerService
    )
    {
    }

    public function index($id):JsonResponse{

        $data = $this->internalTrainerService->getActions($id);

        return $this->sendSuccess('Internal Trainer Actions fetched successfully.',InternalTrainerActionsResource::collection($data), StatusCodeEnum::CREATED->value);
    }

}
