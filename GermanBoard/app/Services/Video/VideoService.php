<?php

namespace App\Services\Video;

use App\Enum\StatusCodeEnum;
use App\Enum\TrainingTypeEnum;
use App\Repositories\Contracts\TrainingRepositoryInterface;
use App\Repositories\Contracts\VideoRepositoryInterface;

class VideoService
{
    public function __construct(
        private VideoRepositoryInterface    $videoRepository,
        private TrainingRepositoryInterface $trainingRepository,
    )
    {
    }

    public function create(array $data): void
    {
        $user = auth()->user();
        if ($user->hasRole('trainee')) {
            throw new \DomainException('only providers can add videos', StatusCodeEnum::UNAUTHORIZED->value);
        }

        $provider = $user->provider;
        $training = $this->trainingRepository->getById($data['training_id']);
        if ($provider->id !== $training->provider_id) {
            throw new \DomainException('you can only add videos for your trainings', StatusCodeEnum::UNAUTHORIZED->value);
        }

        if ($training->type !== TrainingTypeEnum::RECORDED->value) {
            throw new \DomainException('videos can only be added to recorded trainings', StatusCodeEnum::FORBIDDEN->value);
        }

        $video = $data['video'];

        $filename = $video->getClientOriginalName();

        $path = $video->storeAs('videos' . '/' . $training->title_en . '/' . $data['position'], $filename, 'public');

        $video_data = [
            'training_id' => $data['training_id'],
            'position' => $data['position'],
            'src' => $path,
            'title' => $data['title'],
            'description' => $data['description'],
        ];

        $this->videoRepository->create($video_data);
    }
    public function edit(array $data): array
    {
        $user = request()->user();

        if ($user->hasRole('trainee') || $user->hasRole('admin')) {
            throw new \DomainException('only providers can do this', StatusCodeEnum::UNAUTHORIZED->value);
        }

        $video = $this->videoRepository->getWithRelations($data['video_id']);

        if ($video->training->provider_id !== $user->provider->id) {
            throw new \DomainException('you can only edit videos in your trainings', StatusCodeEnum::FORBIDDEN->value);
        }

        $video->title = $data['title'];
        $video->description = $data['description'];
        $video->position = $data['position'];
        $this->videoRepository->update($video);
        return [
            'title' => $video->title,
            'description' => $video->description,
            'position' => $video->position,
            'id' => $video->id,
        ];
    }
    public function getTitles(int $training_id): array
    {
        $training = $this->trainingRepository->getById($training_id);

        if(!$training){
            throw new \DomainException("Training not found" , StatusCodeEnum::NOT_FOUND->value);

        }

        if (auth()->user()->cannot('ownTraining', $training)) {
            throw new \DomainException("You are not authorized" , StatusCodeEnum::UNAUTHORIZED->value);
        }

        if($training->type !== TrainingTypeEnum::RECORDED->value){
            throw new \DomainException("Not a recorded training" , StatusCodeEnum::BAD_REQUEST->value);
        }

        $response = $this->videoRepository->getTitles($training_id);

        return $response;
    }
}
