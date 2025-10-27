<?php

namespace App\Http\Controllers\API\V1\Certificate;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Certificate\CertificateByCodeRequest;
use App\Http\Requests\Api\Certificate\UpdateCertificateRequest;
use App\Http\Requests\Api\Certificate\UploadCertificateRequest;
use App\Http\Resources\Certificates\CertificateByCodeResource;
use App\Http\Resources\Certificates\IndexCertificateResource;
use App\Http\Resources\Certificates\NotAssignedTraineesResource;
use App\Services\Certificate\CertificateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificateController extends BaseApiController
{
    public function __construct(
        private CertificateService $certificateService
    ) {}

    public function index(): JsonResponse
    {
        $data = $this->certificateService->index();
        $certificates = IndexCertificateResource::collection($data);
        $response = [
            'certificates' => $certificates,
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'per_page' => $data->perPage(),
            'total' => $data->total(),
            ];
        return $this->sendSuccess('trainee with no certificates', $response, StatusCodeEnum::OK->value);
    }

    public function unAssignedCertificates(): JsonResponse
    {
        $data = $this->certificateService->unAssignedCertificates();
        return $this->sendSuccess('certificate', NotAssignedTraineesResource::collection($data), StatusCodeEnum::OK->value);
    }

    public function upload(UploadCertificateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->certificateService->upload($data);
        return $this->sendSuccess('certificate uploaded', [], StatusCodeEnum::OK->value);
    }

//    public function update(UpdateCertificateRequest $request): JsonResponse
//    {
//        $data = $request->validated();
//        $this->certificateService->updateCertificate($data);
//        return $this->sendSuccess('certificate updated', [], StatusCodeEnum::OK->value);
//    }

    public function delete(int $training_trainee_id): JsonResponse
    {
        $this->certificateService->deleteCertificate($training_trainee_id);
        return $this->sendSuccess('certificate deleted', [], StatusCodeEnum::OK->value);
    }

    public function byCode(CertificateByCodeRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->certificateService->byCode($data);
        return $this->sendSuccess('certificate fetched', CertificateByCodeResource::make($response), StatusCodeEnum::OK->value);
    }
}
