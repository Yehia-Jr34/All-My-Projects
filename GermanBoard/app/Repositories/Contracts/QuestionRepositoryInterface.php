<?php

namespace App\Repositories\Contracts;

use App\Models\Question;

interface QuestionRepositoryInterface
{
    public function store(array $data): array;

    public function show(int $id) : Question;

    public function edit(int $id , array $data) : bool;

    public function showWithAnswers(array $questions_ids);


}
