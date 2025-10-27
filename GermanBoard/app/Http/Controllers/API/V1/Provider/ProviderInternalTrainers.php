<?php

namespace App\Http\Controllers\API\V1\Provider;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Resources\InternalTrainer\ProviderInternalTrainersResource;
use App\Services\Provider\ProviderService;
use Illuminate\Http\JsonResponse;

class ProviderInternalTrainers extends BaseApiController
{
    public function __construct(
        private readonly ProviderService $providerService
    ) {}

    public function index($provider_id): JsonResponse
    {
        $provider_with_internal_trainers = $this->providerService->internalTrainers($provider_id);

        return $this->sendSuccess('fetched successfully', ProviderInternalTrainersResource::collection($provider_with_internal_trainers->internalTrainers), StatusCodeEnum::OK->value);
    }
}
