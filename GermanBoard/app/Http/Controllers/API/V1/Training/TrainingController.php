<?php

namespace App\Http\Controllers\API\V1\Training;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\Api\Payment\PaymentResponseRequest;
use App\Http\Requests\Api\Quiz\EditQuizRequest;
use App\Http\Requests\Api\Trainee\EnrollRequest;
use App\Http\Requests\Api\Training\AddAttachmentsRequest;
use App\Http\Requests\Api\Training\AddSessionsRequest;
use App\Http\Requests\Api\Training\CreateTrainingRequest;
use App\Http\Requests\Api\Training\EditSessionStatus;
use App\Http\Requests\Api\Training\EditSessionsTimeAndDataRequest;
use App\Http\Requests\Api\Training\GetTrainingDetailsRequest;
use App\Http\Requests\Api\Training\MakeTraineePassTheTrainingRequest;
use App\Http\Requests\Api\Training\UpdateTrainingCoverRequest;
use App\Http\Requests\Api\Training\UpdateTrainingRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Exam\QuizResource;
use App\Http\Resources\Trainee\EnrolledTrainingResource;
use App\Http\Resources\Training\GetEnrolledTrainingResource;
use App\Http\Resources\Training\GetTrainingResource;
use App\Http\Resources\Training\ListTrainingsResource;
use App\Http\Resources\Training\Live\GetLiveEnrolledTrainingResource;
use App\Http\Resources\Training\LiveTrainingCardResource;
use App\Http\Resources\Training\LiveTrainingDetailsResource;
use App\Http\Resources\Training\RecordedTrainingResource;
use App\Http\Resources\Training\TrainingDetailsResource;
use App\Http\Resources\Training\TrainingHomePageDataResource;
use App\Http\Resources\Training\TrainingTitlesResource;
use App\Services\Training\TrainingService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Trainee\Trainings\RecordedWithMaterialsResource;
use App\Http\Resources\Training\TrainingByCategory;

class TrainingController extends BaseApiController
{
    public function __construct(
        private readonly TrainingService $trainingService,
    ) {}

    // Provider
    public function create(CreateTrainingRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->trainingService->create($data);

        return $this->sendSuccess('Course added successfully', $response, 201);
    }

    public function addSessions(AddSessionsRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->trainingService->addSessions($data);

        return $this->sendSuccess('sessions added successfully', [], 201);
    }

    public function addAttachments(AddAttachmentsRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->trainingService->addAttachments($data);

        return $this->sendSuccess('attachments added successfully', [], 201);
    }

    public function editSessionDatesAndTimes(EditSessionsTimeAndDataRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->trainingService->editSessionDatesAndTimes($data);

        return $this->sendSuccess('Session dates and times updated successfully', $response, 200);
    }

    public function editSessionStatus(EditSessionStatus $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->trainingService->editSessionStatus($data);

        return $this->sendSuccess('Session status updated successfully', $response, 200);
    }

    public function makeTraineePassTheTraining(MakeTraineePassTheTrainingRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->trainingService->makeTraineePassTheTraining($data);

        return $this->sendSuccess('Trainee marked as passed the training successfully', [], 200);
    }

    public function getLiveTrainings(): JsonResponse
    {
        $response = $this->trainingService->getLiveTrainings();

        $liveTrainings = LiveTrainingCardResource::collection($response['all_live_trainings']);
        $ongoingLiveTrainings = LiveTrainingCardResource::collection($response['ongoing_live_trainings']);
        $completedLiveTrainings = LiveTrainingCardResource::collection($response['completed_live_trainings']);
        $notStartedLiveTrainings = LiveTrainingCardResource::collection($response['not_started_live_trainings']);

        return $this->sendSuccess('Live trainings fetched successfully', [
            'liveTrainings' => $liveTrainings,
            'ongoingLiveTrainings' => $ongoingLiveTrainings,
            'completedLiveTrainings' => $completedLiveTrainings,
            'notStartedLiveTrainings' => $notStartedLiveTrainings,
        ], 200);
    }

    public function getLiveTrainingDetailsForProvider($training_id): JsonResponse
    {
        $response = $this->trainingService->getLiveTrainingDetailsForProvider($training_id);

        return $this->sendSuccess('Training details fetched successfully', LiveTrainingDetailsResource::make($response), 200);
    }

    public function edit_exam(EditQuizRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->trainingService->edit_exam($data);
        return $this->sendSuccess('Quiz edited successfully', $response, 200);
    }

    // Trainee
    public function enroll(EnrollRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->trainingService->enroll($data);

        return $this->sendSuccess('Enrolled successfully', $response, 200);
    }

    public function payment_response(PaymentResponseRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->trainingService->payment_response($data);

        return $this->sendSuccess('payment process succeeded', [], 200);
    }

    public function getDataForHomePage(): JsonResponse
    {
        $response = $this->trainingService->getDataForHomePage();

        $trainings = TrainingHomePageDataResource::collection($response['trainings']);
        $categories = CategoryResource::collection($response['categories']);

        return $this->sendSuccess('Data fetched successfully', [
            'trainings' => $trainings,
            'categories' => $categories,
        ], 200);
    }

    public function getVideosAndExams(int $training_id): JsonResponse
    {
        $data = $this->trainingService->getVideosAndExams($training_id);

        return $this->sendSuccess('videos and exams retrieved successfully', RecordedWithMaterialsResource::make($data), StatusCodeEnum::OK->value);
    }

    public function watchedVideo(int $video_id): JsonResponse
    {
        $this->trainingService->watchedVideo($video_id);
        return $this->sendSuccess('video watched successfully', [], 200);
    }

    // shared
    public function getTrainingDetails(GetTrainingDetailsRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->trainingService->getTrainingDetails($data);

        return $this->sendSuccess('Training details fetched successfully', TrainingDetailsResource::make($response), 200);
    }

    public function getTraining(GetTrainingDetailsRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->trainingService->getTraining($data);

        return $this->sendSuccess('Training fetched successfully', GetTrainingResource::make($response), 200);
    }

    public function getTitles(): JsonResponse
    {

        $response = $this->trainingService->getTrainingTitles();

        return $this->sendSuccess('Training fetched successfully', TrainingTitlesResource::collection($response), 200);
    }

    public function byCategory(int $category_id): JsonResponse
    {
        $response = $this->trainingService->byCategory($category_id);

        return $this->sendSuccess('Trainings fetched successfully', TrainingByCategory::collection($response), 200);
    }

    public function updateTraining(UpdateTrainingRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->trainingService->updateTraining($data);

        return $this->sendSuccess('Training updated successfully', [], 200);
    }

    public function updateTrainingCover(UpdateTrainingCoverRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->trainingService->updateTrainingCover($data);

        return $this->sendSuccess('Training updated successfully', [], 200);
    }

    public function listTrainings(): JsonResponse{
        $data = $this->trainingService->listTrainings();

        $response = [
            'current_page' => $data->currentPage(),
            'per_page' => $data->perPage(),
            'total' => $data->total(),
            'data' => ListTrainingsResource::collection($data->all()),
        ];

        return $this->sendSuccess('Trainings fetched successfully', $response, 200);

    }

    public function showTraining($id): JsonResponse
    {

        $response = $this->trainingService->getTraining(['training_id' => $id]);

        return $this->sendSuccess('Training fetched successfully', GetTrainingResource::make($response), 200);
    }
}
