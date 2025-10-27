<?php


declare(strict_types=1);

namespace App\Services\Trainee;

use App\Enum\StatusCodeEnum;
use App\Models\Trainee;
use App\Repositories\Contracts\TraineeRepositoryInterface;
use App\Repositories\Contracts\TrainingTraineeRepositoryInterface;
use Illuminate\Support\Collection;

class TraineeService
{

    public function __construct(
        private readonly TraineeRepositoryInterface $traineeRepository,
        private readonly TrainingTraineeRepositoryInterface $trainingTraineeRepository
    ) {}

    public function getMyTrainings(): Trainee
    {

        $user = request()->user();

        $trainee = $this->traineeRepository->getMyTrainings($user->trainee->id);

        return $trainee;
    }

    public function index()
    {
        $trainees = $this->traineeRepository->index();

        return $trainees;
    }

    public function show($trainee_id)
    {
        $trainee = $this->traineeRepository->show($trainee_id);

        if (!$trainee) {
            $this->notFound();
        }

        return $trainee;
    }

    public function ensure_enrolled(int $training_id): bool
    {
        $user = request()->user();

        $tmp = $this->trainingTraineeRepository->ensure_enrolled($training_id, $user->trainee->id);

        if (!$tmp) {
            return false;
        }
        return true;
    }


    // helper methods

    public function notFound()
    {
        throw new \DomainException('Trainee not found', StatusCodeEnum::NOT_FOUND->value);
    }
}
