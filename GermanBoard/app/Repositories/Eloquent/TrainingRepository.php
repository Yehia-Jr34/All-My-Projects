<?php

namespace App\Repositories\Eloquent;

use App\Enum\TrainingTypeEnum;
use App\Models\Training;
use App\Repositories\Contracts\TrainingRepositoryInterface;
use Illuminate\Support\Collection;

class TrainingRepository implements TrainingRepositoryInterface
{
    public function create(array $data): Training
    {
        return Training::create($data);
    }

    public function getById(int $id): Training | null
    {
        return Training::find($id);
    }

    public function getOwnerId(int $id): Training
    {
        return Training::select('provider_id')
            ->where('id', $id)
            ->first();
    }

    public function getDataForHomePage(): Collection
    {
        return Training::select(
            'id',
            'title_ar',
            'title_en',
            'title_du',
            'about_en',
            'about_ar',
            'about_du',
            'price',
            'type',
            'language',
            'rate',
            'provider_id'
        )
            ->inRandomOrder()
            ->limit(10)
            ->get();
    }

    public function getDetails(int $id): Training
    {
        return Training::with(['provider', 'sessions', 'training_attachments', 'training_rates'])
            ->find($id);
    }

    public function getTraining(int $id): Training | null
    {
        return Training::with([
            'provider',
            'sessions',
            'videos' => function ($query) {
                return $query->orderBy('position');
            },
            'training_rates',
            'training_categories.category',
            'key_learning_objectives',
            'training_tags.tags',
        ])->find($id);
    }

    public function getTrainingTitles(int $provider_id, string $type): Collection
    {
        return Training::where('provider_id', $provider_id)
            ->where('type', $type)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getRecordedTraining(int $id): Training
    {
        return Training::with([
            'provider',
            'training_rates',
            'training_categories.category',
            'key_learning_objectives',
            'training_tags.tags',
            'training_attachments',
            'videos',
            'quizzes',
            'training_trainees.trainee.user'
        ])->find($id);
    }

    public function getTrainings(int $provider_id, string $type): Collection
    {
        return Training::where('provider_id', $provider_id)
            ->where('type', $type)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getOngoingTrainings(int $provider_id, string $type = 'live'): Collection
    {
        return Training::where('provider_id', $provider_id)
            ->where('type', $type)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getCompletedTrainings(int $provider_id, string $type = 'live'): Collection
    {
        return Training::where('provider_id', $provider_id)
            ->where('type', $type)
            ->where('end_date', '<', now())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getNotStartedTrainings(int $provider_id, string $type = 'live'): Collection
    {
        return Training::where('provider_id', $provider_id)
            ->where('type', $type)
            ->where('start_date', '>', now())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getTrainingsByIds(array $training_ids, string $type): Collection
    {
        return Training::whereIn('id', $training_ids)
            ->where('type', $type)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getOngoingTrainingsByIds(array $training_ids, string $type = 'live'): Collection
    {
        return Training::whereIn('id', $training_ids)
            ->where('type', $type)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getCompletedTrainingsByIds(array $training_ids, string $type = 'live'): Collection
    {
        return Training::whereIn('id', $training_ids)
            ->where('type', $type)
            ->where('end_date', '<', now())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getNotStartedTrainingsByIds(array $training_ids, string $type = 'live'): Collection
    {
        return Training::whereIn('id', $training_ids)
            ->where('type', $type)
            ->where('start_date', '>', now())
            ->orderBy('created_at', 'desc')
            ->get();
    }


    public function getLiveTraining($training_id): Training
    {
        return Training::with([
            'sessions',
            'training_rates',
            'training_trainees.trainee.user',
            'key_learning_objectives',
            'training_categories.category',
            'training_tags.tags',
            'sessions',
        ])
            ->find($training_id);
    }

    public function getTrainingWithTraineesById(int $training_id): Training
    {
        return Training::where('id', $training_id)
            ->with([
                'training_trainees',
                'training_trainees.trainee',
                'training_trainees.trainee.certificates'
            ])
            ->first();
    }

    public function isAdminTraining(int $training_id): Training | null
    {
        return Training::find($training_id);
    }

    public function byCategory(int $category_id): Collection
    {
        return Training::whereHas('training_categories', function ($query) use ($category_id) {
            $query->where('category_id', $category_id);
        })
            ->with('provider')
            ->get();
    }

    public function search(string $query): Collection|null
    {
        $local = app()->getLocale();
        $attr = 'title_' . $local;
        return Training::with(['training_trainees','provider'])->where($attr, 'like', "%$query%")->get();
    }

    public function getByProvider($provider_id): Collection
    {
        return Training::with('training_trainees')->where('provider_id', $provider_id)->get();
    }

    public function getAll(): Collection
    {
        return Training::all();
    }

    public function update(int $id, array $data): bool
    {
        return Training::find($id)->update($data);
    }
}
