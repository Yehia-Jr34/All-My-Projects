<?php

namespace App\Repositories\Eloquent;

use App\Models\Answer;
use App\Repositories\Contracts\AnswerRepositoryInterface;

class AnswerRepository implements AnswerRepositoryInterface
{

    public function store(array $data): array
    {
        return Answer::create($data)->toArray();
    }

    public function insert(array $data): void
    {
        Answer::insert($data);
    }

    public function edit(int $id, array $data, int $question_id): void
    {
        Answer::where('id', $id)->where('question_id', $question_id)->first()?->update($data);
    }

    public function delete(array $answers_id): void
    {
        Answer::destroy($answers_id);
    }

    public function show(int $id): Answer | null
    {
        return Answer::find($id);
    }
}
