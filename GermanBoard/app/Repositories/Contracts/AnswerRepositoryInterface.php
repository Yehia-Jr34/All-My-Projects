<?php

namespace App\Repositories\Contracts;

use App\Models\Answer;

interface AnswerRepositoryInterface
{
    public function store(array $data): array;

    public function insert(array $data): void;

    public function edit(int $id, array $data, int $question_id): void;

    public function delete(array $answers_id): void;

    public function show(int $id): Answer | null;
}
