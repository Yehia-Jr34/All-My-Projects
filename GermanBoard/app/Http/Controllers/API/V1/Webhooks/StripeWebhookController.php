<?php

namespace App\Http\Controllers\API\V1\Webhooks;

use App\Http\Controllers\API\BaseApiController;
use App\Services\Webhook\StripeWebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends baseApiController
{
    public function __construct(
        private StripeWebhookService $stripeWebhookService,
    ) {}

    public function handleWebhook(Request $request): JsonResponse
    {
         $this->stripeWebhookService->handleWebhook($request);

        return $this->sendSuccess('user paid successfully');
    }
}
