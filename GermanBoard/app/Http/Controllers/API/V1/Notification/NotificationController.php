<?php

namespace App\Http\Controllers\API\V1\Notification;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\Api\Quiz\CreateQuizRequest;
use App\Http\Requests\Api\Quiz\SubmitQuizRequest;
use App\Http\Resources\Exam\QuizForTraineeResource;
use App\Http\Resources\Exam\QuizResource;
use App\Services\Notification\NotificationService;
use App\Services\Quizzes\QuizService;
use Illuminate\Http\JsonResponse;

class NotificationController extends BaseApiController
{
    public function __construct(
        private NotificationService $notificationSerice
    ) {}

    public function getMyProviderNotification(): JsonResponse
    {
        $data = $this->notificationSerice->getMyProviderNotification();
        return $this->sendSuccess('Notification fetched successfully', $data);

    }
    public function isThereUnreadNotifications(): JsonResponse
    {
        $data = $this->notificationSerice->isThereUnreadNotifications();
        return $this->sendSuccess('Notification fetched successfully', $data);
    }


}
