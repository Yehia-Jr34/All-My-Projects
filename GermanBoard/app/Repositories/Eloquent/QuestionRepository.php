<?php

namespace App\Repositories\Eloquent;

use App\Models\Question;
use App\Repositories\Contracts\QuestionRepositoryInterface;

class QuestionRepository implements QuestionRepositoryInterface
{

    public function store(array $data): array
    {
        return Question::create($data)->toArray();
    }

    public function show(int $id): Question
    {
        return Question::with(['quiz' , 'answers'])->find($id);
    }

    public function edit(int $id, array $data): bool
    {
        return Question::with('quiz')->find($id)?->update($data);
    }

    public function showWithAnswers(array $questions_ids)
    {
        return Question::with('answers')
            ->whereIn('id', $questions_ids)
            ->get();
    }
}
