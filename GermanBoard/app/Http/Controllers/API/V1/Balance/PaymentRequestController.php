<?php

namespace App\Http\Controllers\API\V1\Balance;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\Api\PaymentRequest\CreatePaymentRequestRequest;
use App\Http\Resources\Balance\PaymentRequestAdminAllResource;
use App\Services\PaymentRequest\PaymentRequestService;
use Illuminate\Http\JsonResponse;

class PaymentRequestController extends BaseApiController
{
    public function __construct(
        private PaymentRequestService $paymentRequestService
    ){}
    public function create(CreatePaymentRequestRequest $request):JsonResponse{

        $this->paymentRequestService->create($request->validated());

        return $this->sendSuccess('created successfully');
    }

    public function getMy():JsonResponse{

        $data = $this->paymentRequestService->getMy();

        return $this->sendSuccess('fetched successfully',$data);
    }

    public function getSummary():JsonResponse{

        $data = $this->paymentRequestService->getSummary();

        return $this->sendSuccess('fetched successfully',$data);
    }

    public function all():JsonResponse{

        $data = $this->paymentRequestService->all();

        return $this->sendSuccess('fetched successfully',PaymentRequestAdminAllResource::collection($data));
    }

    public function approve($id):JsonResponse{

        $this->paymentRequestService->approve($id);

        return $this->sendSuccess('approved successfully');
    }

    public function getAdminSummary():JsonResponse{

        $data = $this->paymentRequestService->getAdminSummary();

        return $this->sendSuccess('fetched successfully',$data);
    }

}
