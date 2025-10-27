<?php

namespace App\Services\Training;

use App\Enum\AppConstants;
use App\Enum\NotificationTypesEnum;
use App\Enum\QuizzesStatusEnum;
use App\Enum\RolesEnum;
use App\Enum\SessionStatusEnum;
use App\Enum\StatusCodeEnum;
use App\Enum\TrainingMediaCollectionsEnum;
use App\Enum\TrainingTypeEnum;
use App\Jobs\SendNotification;
use App\Models\Notification;
use App\Models\TraineeTrainingQuiz;
use App\Models\TraineeVideo;
use App\Models\Training;
use App\Models\TrainingCategory;
use App\Models\TrainingTrainee;
use App\Models\User;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\InternalTrainerActionsRepositoryInterface;
use App\Repositories\Contracts\KeyLearningObjectiveRepositoryInterface;
use App\Repositories\Contracts\PaymentIntentRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\QuizRepositoryInterface;
use App\Repositories\Contracts\TagRepositoryInterface;
use App\Repositories\Contracts\TraineeVideoRepositoryInterface;
use App\Repositories\Contracts\TrainingCategoryRepositoryInterface;
use App\Repositories\Contracts\TrainingRepositoryInterface;
use App\Repositories\Contracts\TrainingSessionsRepositoryInterface;
use App\Repositories\Contracts\TrainingTagRepositoryInterface;
use App\Repositories\Contracts\TrainingTraineeRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class TrainingService
{
    public function __construct(
        private TrainingRepositoryInterface             $trainingRepository,
        private TrainingSessionsRepositoryInterface     $trainingSessionsRepository,
        private TrainingTraineeRepositoryInterface      $trainingTraineeRepository,
        private CategoryRepositoryInterface             $categoryRepository,
        private KeyLearningObjectiveRepositoryInterface $keyLearningObjectiveRepository,
        private TrainingCategoryRepositoryInterface     $trainingCategoryRepository,
        private PaymentRepositoryInterface              $paymentRepository,
        private TagRepositoryInterface                  $tagRepository,
        private TrainingTagRepositoryInterface          $trainingTagRepository,
        private TraineeVideoRepositoryInterface         $traineeVideoRepository,
        private QuizRepositoryInterface                 $quizRepository,
        private InternalTrainerActionsRepositoryInterface $internalTrainerActionsRepository
    ) {}

    // Provider
    public function create(array $data): array
    {
        $user = request()->user();

        if (!$user->hasRole('trainee')) { // should be "$user->hasRole('provider)"
            $training = $this->trainingRepository->create([
                'title_ar' => $data['title_ar'],
                'title_en' => $data['title_en'],
                'title_du' => $data['title_du'],
                'about_ar' => $data['about_ar'],
                'about_en' => $data['about_en'],
                'about_du' => $data['about_du'],
                'price' => $data['price'],
                'type' => $data['type'],
                'start_date' => isset($data['start_date']) === false ? null : $data['start_date'],
                'end_date' => isset($data['end_date']) === false ? null : $data['end_date'],
                'duration_in_hours' => $data['duration_in_hours'],
                'language' => $data['language'],
                'provider_id' => $user->provider->id,
            ]);

            $key_learning_objective_data =
                collect($data['key_learning_objectives'])->map(function ($item) use ($training) {
                    return [
                        'text' => $item,
                        'training_id' => $training->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                });

            $this->keyLearningObjectiveRepository->store($key_learning_objective_data->toArray());

            $tags = collect($data['tags'])->map(function ($item) {
                return [
                    'name' => $item,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            $tags = $this->tagRepository->storeOrFind($tags);

            $training_tag = collect($tags)->map(function ($item) use ($training) {
                return [
                    'training_id' => $training->id,
                    'tag_id' => $item['id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });
            $this->trainingTagRepository->store($training_tag->toArray());

            $this->trainingCategoryRepository->store([
                'category_id' => $data['category_id'],
                'training_id' => $training['id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $training->addMedia($data['cover'])
                ->toMediaCollection('covers');

            $training['cover'] = $training->getFirstMediaUrl('covers');

            if(!$user->hasRole(RolesEnum::ADMIN->value)){
                $adminUser = User::where('email',AppConstants::ADMIN_EMAIL->value)->first();
                $providerName= $user->provider->first_name ." ". $user->provider->last_name;
                Notification::create([
                    'title' => 'Training Added',
                    'body' => "Provider $providerName has been added new training",
                    'user_id' => $adminUser->id,
                    'notification_type' => NotificationTypesEnum::TRAINING_ADDED->value
                ]);
            }

            return $training->toArray();
        } else {
            throw new \DomainException('unauthorized', 401);
        }
    }

    public function addSessions(array $data): void
    {

        DB::transaction(function () use ($data) {

            $training = $this->trainingRepository->getById($data['training_id']);

            if (sizeof($data['sessions']) > 1) {
                throw new \DomainException('Can\'t take more than one', StatusCodeEnum::INTERNAL_SERVER_ERROR->value);
            }

            if ($training['type'] !== TrainingTypeEnum::LIVE->value) {
                throw new \DomainException('you can only add sessions for live training', StatusCodeEnum::BAD_REQUEST->value);
            }

            $user = request()->user();

            if ($user->hasRole('trainee')) {
                throw new \DomainException('only providers can add sessions for training sessions', StatusCodeEnum::UNAUTHORIZED->value);
            }

            if ($provider = $user->provider) {

                if ($training->provider_id !== $provider->id) {
                    throw new \DomainException('you can only add sessions for your trainings', StatusCodeEnum::UNAUTHORIZED->value);
                }
            } elseif ($internalTrainer = $user->internalTrainer) {

                $this->checkInternalTrainerAuthorized($internalTrainer, $training);

                $this->internalTrainerActionsRepository->create([
                    'internal_trainer_id' => $internalTrainer->id,
                    'training_id' => $training->id,
                    'action' => "add session with title:{$data['sessions'][0]['title']}"
                ]);
            }

            $sessionsData = collect($data['sessions'])->map(function ($item) use ($data) {
                return [
                    'training_id' => $data['training_id'],
                    'start_date' => $item['start_date'],
                    'title' => $item['title'],
                    'notes' => $item['notes'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });

            $this->trainingSessionsRepository->add($sessionsData);
        });
    }

    public function addAttachments(array $data): void
    {
        if (sizeof($data['attachments']) > 1) {
            throw new \DomainException('Should be one attachment', StatusCodeEnum::INTERNAL_SERVER_ERROR->value);
        }

        DB::transaction(function () use ($data) {
            $user = request()->user();
            if ($user->hasRole('trainee')) {
                throw new \DomainException('only providers can add attachments for trainings', 400);
            }

            $training = $this->trainingRepository->getById($data['training_id']);

            if ($provider = $user->provider) {
                if ($training->provider_id !== $provider->id) {
                    throw new \DomainException('you can only add attachments for your trainings', StatusCodeEnum::UNAUTHORIZED->value);
                }
            } elseif ($internalTrainer = $user->internalTrainer) {
                $this->checkInternalTrainerAuthorized($internalTrainer, $training);
                $filename = $data['attachments'][0]->getFilename();

                $this->internalTrainerActionsRepository->create([
                    'internal_trainer_id' => $internalTrainer->id,
                    'training_id' => $training->id,
                    'action' => "add attachment file:$filename"
                ]);
            }

            foreach ($data['attachments'] as $attachment) {
                $training->addMedia($attachment)
                    ->toMediaCollection(TrainingMediaCollectionsEnum::ATTACHMENTS->value);
            }

            $trainees_ids = $training->training_trainees->map(function ($training_trainee){
                return $training_trainee->trainee->user->id;
            })->toArray();

            SendNotification::dispatch(
                $trainees_ids,
                "New Attachment",
                "A new attachment has been added to training: $training->title_en",
                NotificationTypesEnum::ATTACHMENT_ADDED
            );


        });
    }

    public function getTraining(array $data): Training|null
    {
        $training = $this->trainingRepository->getTraining($data['training_id']);

        if(!$training) {
        throw new \DomainException("not found", StatusCodeEnum::NOT_FOUND->value);
    }
        return $training;
    }

    public function getLiveTrainingDetailsForProvider($training_id): Training
    {
        $training = $this->trainingRepository->getLiveTraining($training_id);

        if ($training->type !== TrainingTypeEnum::LIVE->value) {
            throw new \DomainException('training is not live', StatusCodeEnum::BAD_REQUEST->value);
        }

        return $training;
    }

    public function editSessionDatesAndTimes(array $data): array
    {
        return DB::transaction(function () use ($data) {

            $session = $this->trainingSessionsRepository->getById($data['session_id']);
            if (!$session) {
                throw new \DomainException('session not found', StatusCodeEnum::NOT_FOUND->value);
            }
            $user = request()->user();
            if ($user->hasRole('trainee')) {
                throw new \DomainException('only providers can do this', StatusCodeEnum::UNAUTHORIZED->value);
            }

            $training = Training::with(['training_trainees.trainee.user'])->find($session->training_id);
            if ($provider = $user->provider) {
                if ($training->provider_id !== $provider->id) {
                    throw new \DomainException('you can only update sessions for your trainings', StatusCodeEnum::UNAUTHORIZED->value);
                }
            } elseif ($internalTrainer = $user->internalTrainer) {
                $this->checkInternalTrainerAuthorized($internalTrainer, $training);
                //                $filename = $data['attachments'][0]->getFilename();

                $this->internalTrainerActionsRepository->create([
                    'internal_trainer_id' => $internalTrainer->id,
                    'training_id' => $training->id,
                    'action' => "edit session :{$data['title']} "
                ]);
            }

            $oldSessionTime = $session->start_date;

            $session->update([
                "start_date"=>$data['start_date'],
                'notes'=>$data['notes'],
                'title' => $data['title']
            ]);

            if($oldSessionTime != $data['start_date']){
                $trainees_ids = $training->training_trainees->map(function ($item){
                    return $item->trainee->user->id;
                })->toArray();
                $session_start_date = $data['start_date'];
                SendNotification::dispatch(
                    $trainees_ids,
                    "Session Modified",
                    "Your Session '$session->title' appointment has been changed to $session_start_date",
                    NotificationTypesEnum::SESSION_MODIFIED
                );
            }
            return $session->toArray();
        });
    }

    public function makeTraineePassTheTraining(array $data): void
    {
        $user = request()->user();
        if ($user->hasRole('trainee')) {
            throw new \DomainException('unauthorized', 401);
        }

        if ($user->hasRole(RolesEnum::INTERNAL_TRAINER->value)) {
            $provider = $user->internalTrainer->providers->first();
        }else {
            $provider = $user->provider->first();
        }

        $training_trainee = $this->trainingTraineeRepository->getById($data['training_trainee_id']);

        if (!$training_trainee) {
            throw new \DomainException('registration not found', StatusCodeEnum::NOT_FOUND->value);
        }

        // if the user is not admin, he can only mark trainees as passed the training on his trainings
        if (!$user->hasRole('admin')) {
            if ($training_trainee->training->provider_id !== $provider->id) {
                throw new \DomainException('you can only mark trainees as passed the training on your trainings', 401);
            }
        }

        $this->trainingTraineeRepository->update($data['training_trainee_id'], ['passed_the_training' => true]);

        $trainingName = $training_trainee->training->title_en;
        SendNotification::dispatch(
            [$training_trainee->trainee->user->id],
            "Passed Training",
            "You have successfully passed training $trainingName",
            NotificationTypesEnum::PASSED_TRAINING
        );

    }

    public function getLiveTrainings(): array
    {
        $user = request()->user();
        if ($user->hasRole('trainee')) {
            throw new \DomainException('only providers can do this', 401);
        }

        // in case the user is Internal Trainer
        if ($user->hasRole(RolesEnum::INTERNAL_TRAINER->value)) {
            $internal_trainer_trainings = $user->internalTrainer->trainings;
            $internal_trainer_trainings_ids = $internal_trainer_trainings->map(function ($item) {
                return $item->id;
            })->toArray();
            return [
                'all_live_trainings' => $this->trainingRepository->getTrainingsByIds($internal_trainer_trainings_ids, TrainingTypeEnum::LIVE->value),
                'ongoing_live_trainings' => $this->trainingRepository->getOngoingTrainingsByIds($internal_trainer_trainings_ids, TrainingTypeEnum::LIVE->value),
                'completed_live_trainings' => $this->trainingRepository->getOngoingTrainingsByIds($internal_trainer_trainings_ids, TrainingTypeEnum::LIVE->value),
                'not_started_live_trainings' => $this->trainingRepository->getOngoingTrainingsByIds($internal_trainer_trainings_ids, TrainingTypeEnum::LIVE->value),
            ];
        }

        $provider = $user->provider;

        $all_live_trainings = $this->trainingRepository->getTrainings($provider->id, TrainingTypeEnum::LIVE->value);
        $ongoing_live_trainings = $this->trainingRepository->getOngoingTrainings($provider->id, TrainingTypeEnum::LIVE->value);
        $completed_live_trainings = $this->trainingRepository->getCompletedTrainings($provider->id, TrainingTypeEnum::LIVE->value);
        $not_started_live_trainings = $this->trainingRepository->getNotStartedTrainings($provider->id, TrainingTypeEnum::LIVE->value);

        return [
            'all_live_trainings' => $all_live_trainings,
            'ongoing_live_trainings' => $ongoing_live_trainings,
            'completed_live_trainings' => $completed_live_trainings,
            'not_started_live_trainings' => $not_started_live_trainings,
        ];
    }

    public function edit_exam(array $data): array
    {
        $user = request()->user();
        if ($user->hasRole('trainee')) {
            throw new \DomainException('only providers can do this', StatusCodeEnum::UNAUTHORIZED->value);
        }

        $quiz = $this->quizRepository->getWithRelations($data['quiz_id']);

        if ($quiz->training->provider_id !== $user->provider->id) {
            throw new \DomainException('you can edit only quizzes on your training', StatusCodeEnum::UNAUTHORIZED->value);
        }

        $quiz->title = $data['title'];
        $quiz->video_id = $data['video_id'];
        $quiz->passing_score = $data['passing_score'];
        $quiz->description = $data['description'];

        $this->quizRepository->update($quiz);

        return [
            'title' => $data['title'],
            'video_id' => $data['video_id'],
            'passing_score' => $data['passing_score'],
            'description' => $data['description'],
        ];
    }

    // Trainee
    public function enroll(array $data): array
    {

        return DB::transaction(function () use ($data) {
            Stripe::setApiKey(config('services.stripe.secret'));

            $user = request()->user();

            if (!$user->hasRole('trainee')) {
                throw new \DomainException('unauthorized', 401);
            }

            $training_trainee_with_payment = $this->trainingTraineeRepository->getByTrainingIdAndTraineeId($data['training_id'], $user->trainee->id);

            $training = Training::find($data['training_id']);

            if ($training->price != $data['amount']) {
                throw new \DomainException('amount not the same', 401);
            }

            $has_successful_payment = $training_trainee_with_payment?->payments?->where('status', 'success')->count() > 0 ?? false;

            if ($has_successful_payment) {
                throw new \DomainException('trainee already enrolled before', 403);
            }

            $registration = $training_trainee_with_payment;

            if (!$registration) {
                $registration = $this->trainingTraineeRepository->store([
                    'trainee_id' => $user->trainee->id,
                    'training_id' => $data['training_id'],
                ]);
            }

            $payment_intent = PaymentIntent::create([
                'amount' => (int)$data['amount'] * 100,
                'currency' => $data['currency'],
                'metadata' => [
                    'user_id' => $user->id,
                    'training_name' => $registration->training->title_en,
                    'registration_id' => $registration->id,
                ],
            ]);

            $this->paymentRepository->store([
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'payment_intent_id' => $payment_intent->client_secret,
                'training_trainee_id' => $registration->id,
            ]);

            return [
                'clint_secret' => $payment_intent->client_secret,
                'payment_id' => $payment_intent->id,
            ];
        });
    }

    public function payment_response(array $data): void
    {
        $payment_response = $this->paymentRepository->getByClientSecret($data['client_secret'])->toArray();

        if ($payment_response['status'] !== 'success') {
            throw new \DomainException('payment process failed', StatusCodeEnum::BAD_REQUEST->value);
        }
    }

    public function getDataForHomePage(): array
    {
        $trainings = $this->trainingRepository->getDataForHomePage();
        $categories = $this->categoryRepository->getAll();

        return [
            'trainings' => $trainings,
            'categories' => $categories,
        ];
    }

    public function getTrainingDetails(array $data): Training
    {
        $training = $this->trainingRepository->getDetails($data['training_id']);

        return $training;
    }

    public function getRecordedTrainingDetails(int $training_id): Training
    {
        $training = $this->trainingRepository->getRecordedTraining($training_id);

        if (!$training) {
            throw new \DomainException("not found", StatusCodeEnum::NOT_FOUND->value);
        }

        if ($training->type != TrainingTypeEnum::RECORDED->value) {
            throw new \DomainException("this is not recorded training", StatusCodeEnum::BAD_REQUEST->value);
        }

        return $training;
    }

    public function getVideosAndExams(int $training_id)
    {
        $user = request()->user();

        $data = $this->trainingTraineeRepository->getVideosAndExams($training_id, $user->trainee->id);

        if (!$data)
            throw new \DomainException("Registration not Found", StatusCodeEnum::NOT_FOUND->value);

        // making the array of video items which is the videos ordered by position and with isCompleted attribute
        $videosItems = $data->training
            ->videos
            ->map(function ($item) use($user) {
                return $this->videoItem($item, $user->trainee->id);
            });

        // making the quiz items with the information if the trainee has passed it
        $quizzesItems = $data->training
            ->quizzes
            ->map(function ($item) use ($data) {
                return $this->quizItem($item, $data->quizzes);
            });

        // merge the two collections to get the videos with its status and the quiz with its status ordered
        $merged = collect();

        foreach ($videosItems as $video) {
            $merged->push($video);
            $relatedQuizzes = $quizzesItems->where('video_id', $video['video_id']);
            $merged = $merged->merge($relatedQuizzes);
        }

        $isLocked = false;

        $lastResult = $merged->map(function ($item) use (&$isLocked) {
            $newItem = [
                ...$item,
                'isLocked' => $isLocked
            ];
            if ($item['type'] === 'quiz')
                if ($item['status'] === 'not completed' || $item['status'] === 'failed')
                    $isLocked = true;
            return $newItem;
        });

        return collect([
            'items' => $lastResult,
            'training' => $data->training,
            'certification_image' => $data->certificate?->certification_image,
            'certification_code' => $data->certificate?->certification_code,
        ]);
    }

    public function byCategory(int $category_id): Collection
    {
        $trainings = $this->trainingRepository->byCategory($category_id);

        return $trainings;
    }

    public function watchedVideo(int $video_id): void
    {
        DB::transaction(function () use ($video_id) {
            $user = request()->user();
            if (!$user->hasRole('trainee')) {
                throw new \DomainException('unauthorized', StatusCodeEnum::UNAUTHORIZED->value);
            }
            $trainee = $user->trainee;
            $traineeVideo = $trainee->trainee_videos()->where('trainee_id', $trainee->id)->where('video_id', $video_id)->first();
            if ($traineeVideo) {
                throw new \DomainException('already watched', StatusCodeEnum::BAD_REQUEST->value);
            }


            $traineeVideo = $this->traineeVideoRepository->insert($trainee->id, $video_id);

            $training = $traineeVideo->video->training;
            $training_trainee = $this->trainingTraineeRepository->getByTrainingIdAndTraineeId($training->id, $trainee->id);

            $training_videos_ids = $training->videos->map(fn($item) => $item->id);
            $training_quizzes_ids = $training->quizzes->map(fn($item) => $item->id);

            $trainingVideosCount = sizeof($training->videos);
            $trainingQuizzesCount = sizeof($training->quizzes);


            $finishedVideos = TraineeVideo::where('trainee_id', $trainee->id)
                ->where('status', 'completed')
                ->get()
                ->filter(function ($item) use ($training_videos_ids) {
                    return in_array($item->video_id, $training_videos_ids->toArray());
                });

            $finishedQuizzes = TraineeTrainingQuiz::where('training_trainee_id', $training_trainee->id)
                ->where('status', QuizzesStatusEnum::PASSED->value)
                ->whereIn('quiz_id', $training_quizzes_ids)
                ->get();

            $training_trainee = TrainingTrainee::where('training_id', $training->id)
                ->where('trainee_id', $trainee->id)->first();

            $totalProgress = sizeof($finishedQuizzes) + sizeof($finishedVideos);

            $progress = (100 * $totalProgress) / ($trainingQuizzesCount + $trainingVideosCount);

            if ($training_trainee->achievement_percentage < $progress) {
                $training_trainee->update(['achievement_percentage' => $progress]);
            }
        });
    }

    public function getLiveTraining($id): Training
    {
        $user = auth()->user();

        $is_registered = false;

        $training = Training::with(['sessions','training_attachments','provider.user'])
            ->find($id);

        $user->trainee->trainings->each(function ($training) use(&$is_registered , $id){
            if($id == $training->id){
                if($training->pivot->payment_status !== 'success'){
                    throw new \DomainException('You\'re not registered in this training', StatusCodeEnum::UNAUTHORIZED->value);
                }
                $is_registered = true;
            }
        });

        if (!$is_registered ) {
            throw new \DomainException('You\'re not registered in this training', StatusCodeEnum::UNAUTHORIZED->value);
        }

        if (!$training) {
            throw new \DomainException('not found', StatusCodeEnum::NOT_FOUND->value);
        }

        if ($training->type !== TrainingTypeEnum::LIVE->value) {
            throw new \DomainException('this route is for live training ', StatusCodeEnum::BAD_REQUEST->value);
        }

        return $training;
    }

    public function getRecordedTraining($id): array
    {

        $user = auth()->user();

        $trainee = $user->trainee;

        $training = $this->trainingRepository->getTraining($id);

        if (!$training) {
            throw new \DomainException('not found', StatusCodeEnum::NOT_FOUND->value);
        }

        if ($training->type !== TrainingTypeEnum::RECORDED->value) {
            throw new \DomainException('this route is for recorded query', StatusCodeEnum::BAD_REQUEST->value);
        }

        $items = $this->getVideosAndExams($id);

        return [
            'training' => $items['training'],
            'items' => $items['items'],
        ];
    }

    public function getRecordedTrainings(): Collection
    {

        $provider_id = auth()->user()->provider->id;

        return $this->trainingRepository->getTrainings($provider_id, TrainingTypeEnum::RECORDED->value);
    }

    public function getTrainingTitles(): Collection
    {
        $provider_id = auth()->user()->provider->id;

        return $this->trainingRepository->getTrainings($provider_id, TrainingTypeEnum::LIVE->value);
    }

    public function getOnsiteTraining(): array
    {

        $user = request()->user();

        if ($user->hasRole('trainee')) {
            throw new \DomainException('only providers can do this', 401);
        }

        $provider = $user->provider;

        $all_onsite_trainings = $this->trainingRepository->getTrainings($provider->id, TrainingTypeEnum::ONSITE->value);

        $ongoing_onsite_trainings = $this->trainingRepository->getOngoingTrainings($provider->id, TrainingTypeEnum::ONSITE->value);
        $completed_onsite_trainings = $this->trainingRepository->getCompletedTrainings($provider->id, TrainingTypeEnum::ONSITE->value);
        $not_started_onsite_trainings = $this->trainingRepository->getNotStartedTrainings($provider->id, TrainingTypeEnum::ONSITE->value);

        return [
            'all_onsite_trainings' => $all_onsite_trainings,
            'ongoing_onsite_trainings' => $ongoing_onsite_trainings,
            'completed_onsite_trainings' => $completed_onsite_trainings,
            'not_started_onsite_trainings' => $not_started_onsite_trainings,
        ];
    }

    public function getOnsiteTrainingDetail(int $id): Training
    {
        $training = $this->trainingRepository->getTraining($id);

        if (!$training) {
            throw new \DomainException('not found', StatusCodeEnum::NOT_FOUND->value);
        }

        if ($training->type !== TrainingTypeEnum::ONSITE->value) {
            throw new \DomainException('this route is for recorded query', StatusCodeEnum::BAD_REQUEST->value);
        }

        return $training;
    }

    public function isAdminTraining(int $training_id): bool
    {
        $admin = request()->user();
        $admin_provider_id = $admin->provider->id;

        $training = $this->trainingRepository->isAdminTraining($training_id);

        if (!$training) {
            throw new \DomainException('not found', StatusCodeEnum::NOT_FOUND->value);
        }

        return $training->provider_id === $admin_provider_id;
    }

    public function editSessionStatus(array $data)
    {
        return DB::transaction(function () use ($data) {

            $session = $this->trainingSessionsRepository->getById($data['session_id']);
            if (!$session) {
                throw new \DomainException('session not found', StatusCodeEnum::NOT_FOUND->value);
            }
            $user = request()->user();
            if ($user->hasRole('trainee')) {
                throw new \DomainException('only providers can do this', StatusCodeEnum::UNAUTHORIZED->value);
            }

            $training = Training::with(['training_trainees.trainee.user'])->find($session->training_id);
            if ($provider = $user->provider) {
                if ($training->provider_id !== $provider->id) {
                    throw new \DomainException('you can only update sessions for your trainings', StatusCodeEnum::UNAUTHORIZED->value);
                }
            } elseif ($internalTrainer = $user->internalTrainer) {
                $this->checkInternalTrainerAuthorized($internalTrainer, $training);
                //                $filename = $data['attachments'][0]->getFilename();

                $this->internalTrainerActionsRepository->create([
                    'internal_trainer_id' => $internalTrainer->id,
                    'training_id' => $training->id,
                    'action' => "edit session status } "
                ]);
            }


            $session['status'] = $data['status'];

            $this->trainingSessionsRepository->update($session);


            if($data['status'] == SessionStatusEnum::CANCELLED->value){
                $trainees_ids = $training->training_trainees->map(function ($item){
                    return $item->trainee->user->id;
                })->toArray();

                SendNotification::dispatch(
                    $trainees_ids,
                    "Session Cancellation",
                    "Your Session '$session->title' is Canceled",
                    NotificationTypesEnum::SESSION_CANCELED
                );
            }

            /*
            $notification_data = $this->sendNotifications(
                training_id: $session['training_id'],
                title: "session updated",
                body: "the trainer delayed the session to : {$data['date_and_time']}",
                notification_type: 2,
            );
            */

            return $session->toArray();
        });
    }

    public function getMyBalance()
    {
        $user = auth()->user();

        $provider = $user->provider;

        $response = [];

        $trainings = $this->trainingRepository->getByProvider($provider->id);

        $trainings->each(function ($training) use (&$response) {
            $trainingTrainees = $training->training_trainees;
            array_push($response, [
                'training' => [
                    'id' => $training->id,
                    'title_ar' => $training->title_ar,
                    'title_en' => $training->title_en,
                    'title_du' => $training->title_du,
                    'type' => $training->type,
                    'price' => $training->price,
                    'cover_image' => $training->getFirstMediaUrl(TrainingMediaCollectionsEnum::COVERS->value),
                ],
                'enrolled_trainee' => sizeof($trainingTrainees),
                'total_income' => $training->total_income
            ]);
        });

        return collect($response);
    }

    public function updateTraining(array $data)
    {
        DB::transaction(function () use ($data) {
            $training = Training::with(['training_categories', 'key_learning_objectives', 'training_tags.tags'])->find($data['id']);

            if (!$training) {
                throw new \DomainException('training not found', StatusCodeEnum::NOT_FOUND->value);
            }
            // update training in the database
            $allowedColumns = [
                "title_ar",
                "title_en",
                "title_du",
                "about_ar",
                "about_en",
                "about_du",
                "start_date",
                "end_date",
                "duration_in_hours",
                "price",
                "type",
                "language",
                "training_site",
            ];

            $filteredData = array_intersect_key($data, array_flip($allowedColumns));
            $training->update($filteredData);

            // update category if it is changed
            if ($training->training_categories()->first()->category_id != $data['category_id']) {
                $training->training_categories()->delete();
                TrainingCategory::create([
                    'training_id' => $training->id,
                    'category_id' => $data['category_id'],
                ]);
            }

            // update key learning objectives if it is changed

            $training->key_learning_objectives()->delete();
            $key_learning_objective_data =
                collect($data['key_learning_objectives'])->map(function ($item) use ($training) {
                    return [
                        'text' => $item,
                        'training_id' => $training->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                });

            $this->keyLearningObjectiveRepository->store($key_learning_objective_data->toArray());

            $training->training_tags()->delete();
            $tags = collect($data['tags'])->map(function ($item) {
                return [
                    'name' => $item,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            $tags = $this->tagRepository->storeOrFind($tags);

            $training_tag = collect($tags)->map(function ($item) use ($training) {
                return [
                    'training_id' => $training->id,
                    'tag_id' => $item['id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });
            $this->trainingTagRepository->store($training_tag->toArray());


            return $training;
        });
    }

    public function updateTrainingCover(array $data)
    {
        $training = Training::find($data['id']);
        if (!$training) {
            throw new \DomainException('training not found', StatusCodeEnum::NOT_FOUND->value);
        }
        $training->clearMediaCollection(TrainingMediaCollectionsEnum::COVERS->value);

        $training
            ->addMediaFromRequest('cover')
            ->toMediaCollection(TrainingMediaCollectionsEnum::COVERS->value);
    }

    public function listTrainings() {
        $data = Training::with(['provider', 'training_categories'])
            ->where(function($query) {
                // For 'onsite' and 'live' types, only get trainings with end_date > now
                $query->whereIn('type', ['onsite', 'live'])
                    ->where('end_date', '>', now());
            })
            ->orWhere(function($query) {
                // For 'recorded' type, get all regardless of end_date
                $query->where('type', 'recorded');
            })
            ->paginate(10);

        return $data;

    }

    // helper methods



    private function videoItem($item , $trainee_id)
    {

        return [
            'video_id' => $item->id,
            'title' => $item->title,
            'videoUrl' => url("storage/$item->src"),
            'duration' => "not determined",
            'type' => 'video',
            'status' => (bool)$item->trainee_videos()->where('trainee_id' ,$trainee_id)->first(),
        ];
    }

    private function quizItem($item, $quizzes_done)
    {

        $failed = false;

        $completed = false;

        $attempts_to_the_current_quiz = $quizzes_done->filter(function ($quiz_done) use ($item) {
            return $item->id == $quiz_done->quiz_id;
        });
        //         dd($quizzes_done);

        $attempts_to_the_current_quiz->each(function ($attempt) use (&$failed, &$completed) {
            if ($attempt->status == QuizzesStatusEnum::FAILED->value)
                $failed = true;
            if ($attempt->status == QuizzesStatusEnum::PASSED->value)
                $completed = true;
        });

        $status = 'not completed';

        if ($completed)
            $status = 'done';
        if ($failed && !$completed)
            $status = 'failed';


        return [
            'quiz_id' => $item->id,
            'title' => $item->title,
            'type' => 'quiz',
            'status' =>    $status,             // passed , failed , not_done
            'video_id' => $item->video_id
        ];
    }

    private function checkInternalTrainerAuthorized($internalTrainer, $training)
    {
        $own_training = $internalTrainer->trainings->where('id', $training->id)->first();

        if (!$own_training) {
            throw new \DomainException('you can only add sessions for your trainings', StatusCodeEnum::UNAUTHORIZED->value);
        }
    }
}
