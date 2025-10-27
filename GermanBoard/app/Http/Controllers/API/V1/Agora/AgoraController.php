<?php

namespace App\Http\Controllers\API\V1\Agora;

use App\Http\Controllers\API\BaseApiController;
use App\Services\Agora\AgoraService;


class AgoraController extends BaseApiController
{
    public function __construct(
        private readonly AgoraService $agoraService
    )
    {
    }

    public function generateToken($trainingSessionId){

        $rtcToken = $this->agoraService->generateToken($trainingSessionId);

        return $this->sendSuccess('generated successfully' , $rtcToken );
    }
}
