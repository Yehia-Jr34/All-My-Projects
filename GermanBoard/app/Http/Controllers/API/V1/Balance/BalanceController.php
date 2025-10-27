<?php

namespace App\Http\Controllers\API\V1\Balance;

use App\Http\Controllers\API\BaseApiController;
use App\Services\Training\TrainingService;
use Illuminate\Http\JsonResponse;

class BalanceController extends BaseApiController
{
    public function __construct(
        private TrainingService $trainingService
    ){

    }
    public function getMyBalance():JsonResponse{
        $response = $this->trainingService->getMyBalance();

        return $this->sendSuccess('fetched successfully', ($response));
    }

}
