<?php

namespace App\Repositories\Contracts;

use App\Models\Quiz;
use Illuminate\Support\Collection;

interface QuizRepositoryInterface
{
    public function store(array $data): Quiz;

    public function get(int $id): Quiz;
    public function getWithRelations(int $id): Quiz;

    public function update(Quiz $quiz): void;
}
