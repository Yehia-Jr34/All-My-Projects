<?php

namespace App\Repositories\Eloquent;

use App\Models\Quiz;
use App\Repositories\Contracts\QuizRepositoryInterface;
use Illuminate\Support\Collection;

class QuizRepository implements QuizRepositoryInterface
{
    public function store(array $data): Quiz
    {
        return Quiz::create($data);
    }

    public function get(int $id): Quiz
    {
        return Quiz::with([
            'questions',
            'questions.answers',
            'video',
            'training'
        ])->find($id);
    }

    public function getWithRelations(int $id): Quiz
    {
        return Quiz::with([
            'questions',
            'questions.answers',
            'training',
        ])->find($id);
    }

    public function update(Quiz $quiz): void
    {
        $quiz->save();
    }
}
