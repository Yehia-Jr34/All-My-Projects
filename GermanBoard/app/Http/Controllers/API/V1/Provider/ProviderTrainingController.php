<?php

namespace App\Http\Controllers\API\V1\Provider;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Resources\Provider\ProviderTrainingResource;
use App\Services\Provider\ProviderService;
use Illuminate\Http\JsonResponse;

class ProviderTrainingController extends BaseApiController
{
    public function __construct(
        private readonly ProviderService $providerService
    ) {}

    public function index($provider_id): JsonResponse
    {
        $provider = $this->providerService->trainings($provider_id);

        return $this->sendSuccess('fetched successfully', ProviderTrainingResource::make($provider), StatusCodeEnum::OK->value);
    }

    public function get_trainees(int $training_id): JsonResponse
    {
        $data = $this->providerService->trainees($training_id);
        return $this->sendSuccess('fetched successfully', $data, StatusCodeEnum::OK->value);
    }
}
