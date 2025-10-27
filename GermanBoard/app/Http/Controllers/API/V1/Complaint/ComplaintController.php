<?php

namespace App\Http\Controllers\API\V1\Complaint;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Complaint\AnswerComplaintRequest;
use App\Http\Requests\Api\Complaint\CreateComplaintRequest;
use App\Services\Complaint\ComplaintService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComplaintController extends BaseApiController
{
    public function __construct(
        private ComplaintService $complaintService
    )
    {
    }

    public function create(CreateComplaintRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->complaintService->create($data);
        return  $this->sendSuccess('Complaint successfully created', $response, StatusCodeEnum::CREATED->value);
    }

    public function listAll(): JsonResponse
    {
        $response = $this->complaintService->listAll();
        return $this->sendSuccess('List all complaints', $response, StatusCodeEnum::OK->value);
    }

    public function getById(int $id): JsonResponse
    {
        $response = $this->complaintService->getById($id);
        return $this->sendSuccess('Complaint successfully fetched', $response, StatusCodeEnum::OK->value);
    }

    public function answer(AnswerComplaintRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->complaintService->answer($data);
        return  $this->sendSuccess('Answer complaint', $response, StatusCodeEnum::OK->value);
    }
}
