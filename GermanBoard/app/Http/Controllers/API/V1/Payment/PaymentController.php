<?php

namespace App\Http\Controllers\API\V1\Payment;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\Api\Payment\PaymentIntentRequest;
use App\Services\Webhook\PaymentService;
use Illuminate\Http\JsonResponse;

class PaymentController extends BaseApiController
{
    public function __construct(
        private PaymentService $paymentService,
    ) {}

    private function createPaymentIntent(PaymentIntentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->paymentService->createPaymentIntent($data);

        return $this->sendSuccess('Payment Intent created', $response, 201);
    }
}
