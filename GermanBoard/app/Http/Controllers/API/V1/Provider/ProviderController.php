<?php

namespace App\Http\Controllers\API\V1\Provider;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\Api\Membership\CreateProviderRequest;
use App\Http\Requests\Api\provider\CompleteProviderInfoRequest;
use App\Http\Resources\Provider\ProviderForTraineeResource;
use App\Services\Provider\ProviderService;
use Illuminate\Http\JsonResponse;

class ProviderController extends BaseApiController
{
    public function __construct(
        private ProviderService $providerService
    ) {}

    public function create(CreateProviderRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->providerService->create($data);
        return $this->sendSuccess('Provider created successfully', $response, StatusCodeEnum::CREATED->value);
    }

    // list all providers with membership filtered by expired_at
    public function index(): JsonResponse
    {
        $response = $this->providerService->index()->toArray();

        return $this->sendSuccess('Provider listed successfully', $response, StatusCodeEnum::OK->value);
    }

    // show provider details
    public function show($provider_id): JsonResponse
    {
        $data = $this->providerService->show($provider_id);
        return $this->sendSuccess('Provider fetched successfully', $data);
    }

    public function complete(CompleteProviderInfoRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->providerService->complete($data);
        return $this->sendSuccess('Providers information completed successfully', $response, StatusCodeEnum::OK->value);
    }

    public function getProviderForTrainee($id): JsonResponse
    {
        $response = $this->providerService->getProviderForTrainee($id);
        return $this->sendSuccess('Providers information fetched successfully', ProviderForTraineeResource::make($response), StatusCodeEnum::OK->value);
    }
}
