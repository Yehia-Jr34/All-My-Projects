<?php

namespace App\Repositories\Eloquent;

use App\Models\TrainingTrainee;
use App\Repositories\Contracts\TrainingTraineeRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TrainingTraineeRepository implements TrainingTraineeRepositoryInterface
{
    public function store(array $data): TrainingTrainee
    {
        $training_trainee = TrainingTrainee::create($data);
        return $training_trainee;
    }

    public function getById(int $training_trainee_id): ?TrainingTrainee
    {
        return TrainingTrainee::find($training_trainee_id);
    }

    public function update(int $training_trainee_id, array $data): void
    {
        $trainingTrainee = TrainingTrainee::find($training_trainee_id);
        $trainingTrainee->update($data);
    }

    public function getTraineeIdsByTrainingId(int $training_id): array
    {
        return TrainingTrainee::select('trainee_id')
            ->where('Training_id', $training_id)
            ->get()->toArray();
    }

    public function getTraineeByPaymentIntent(string $payment_intent): ?TrainingTrainee
    {
        return TrainingTrainee::where('payment_intent_id', $payment_intent)
            ->first();
    }

    public function checkIfUserEnrolledBefore(int $trainee_id, int $training_id): ?TrainingTrainee
    {
        return TrainingTrainee::where('trainee_id', $trainee_id)
            ->where('training_id', $training_id)
            ->where('payment_status', 'success')
            ->first();
    }

    public function getEnrolledTrainings(int $id): Collection
    {
        return TrainingTrainee::with([
            'training',
            'training.provider'
        ])
            ->where('trainee_id', $id)
            ->where('status', 'accepted')
            ->get();
    }

    public function getVideosAndExams(int $training_id, int $trainee_id): TrainingTrainee | null
    {
        return TrainingTrainee::with([
            'training',
            'training.videos' => function ($query) {
                $query->orderBy('position'); // Order videos by position
            },
            'training.videos.trainee_videos',
            'training.quizzes',
            'training.training_attachments',
            'training.training_categories',
            'training.training_tags',
            'training.provider',
            'quizzes',
            'certificate'
        ])->select(['id', 'training_id'])
            ->where('trainee_id', $trainee_id)

            ->whereHas('training', function ($query) use ($training_id) {
                return $query->where('training_id', $training_id);
            })

            ->first();
    }

    public function getLiveEnrolled(int $training_id): Collection
    {
        return TrainingTrainee::with([
            'training',
            'training.provider',
        ])
            ->where('trainee_id', $training_id)
            ->where('status', 'accepted')
            ->where('passed_the_training', false)
            ->whereHas('training', function ($query) {
                $query->where('type', 'live');
            })
            ->get();
    }

    public function trainees(int $training_id): Collection
    {
        return TrainingTrainee::with([
            'trainee',
            'trainee.user'
        ])->where('training_id', $training_id)
            ->where('status', 'accepted')
            ->get();
    }

    public function index(): Collection
    {
        $certificates =  TrainingTrainee::with([
            'trainee',
            'trainee.user',
            'training',
        ])->whereNotNull('certification_image')
            ->get();

        $nullCertCount = TrainingTrainee::whereNull('certification_image')->where('passed_the_training', true)->count();

        return collect([
            'certificates' => $certificates,
            'unissued_certificates' => $nullCertCount,
        ]);
    }

    public function unAssignedCertificates(): Collection
    {
        return TrainingTrainee::with([
            'trainee',
            'trainee.user',
            'training.provider.user',
        ])->whereDoesntHave('certificate')
        ->get();
    }

    public function updateByTrainingIdAndTraineeId(int $training_id, int $trainee_id, array $data): bool
    {
        $trainingTrainee = TrainingTrainee::where('training_id', $training_id)
            ->where('trainee_id', $trainee_id);

        if ($trainingTrainee->count() > 1) {
            return false;
        }
        if (!$trainingTrainee->first()->passed_the_training) {
            return false;
        }
        return $trainingTrainee->update($data);
    }

    public function getByTrainingIdAndTraineeId(int $training_id, int $trainee_id): ?TrainingTrainee
    {
        return TrainingTrainee::where('training_id', $training_id)
            ->where('trainee_id', $trainee_id)
            ->with('payments')
            ->first();
    }

    public function getByCertificationCode(string $code): ?TrainingTrainee
    {
        return TrainingTrainee::where('certification_code', $code)
            ->first();
    }

    public function ensure_enrolled(int $training_id, int $trainee_id): TrainingTrainee|null
    {
        return TrainingTrainee::where('training_id', $training_id)
            ->where('trainee_id', $trainee_id)
            ->where('payment_status', 'success')
            ->first();
    }
}
