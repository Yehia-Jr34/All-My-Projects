<?php

namespace App\Http\Controllers\API\V1\Trainee;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\Api\Trainee\EnsureEnrolled;
use App\Http\Resources\Trainee\TraineeDetailAdminResource;
use App\Http\Resources\Trainee\TraineeRecordResource;
use App\Http\Resources\Trainee\TraineeResource;
use App\Services\Trainee\TraineeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class TraineeController extends BaseApiController
{

    public function __construct(
        private readonly TraineeService $traineeService
    ) {}

    public function index(): JsonResponse
    {
        $response = $this->traineeService->index();

        return $this->sendSuccess("fetched successfully", TraineeRecordResource::collection($response), StatusCodeEnum::OK->value);
    }

    public function show($trainee_id)
    {
        $response = $this->traineeService->show($trainee_id);

        return $this->sendSuccess("fetched successfully", TraineeDetailAdminResource::make($response), StatusCodeEnum::OK->value);
    }

    public function ensure_enrolled(EnsureEnrolled $request): JsonResponse
    {
        $result = $this->traineeService->ensure_enrolled($request->training_id);
        return $this->sendSuccess('user enrolled before', $result, StatusCodeEnum::OK->value);
    }
}
