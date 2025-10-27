<?php

namespace App\Repositories\Contracts;

interface TrainingCategoryRepositoryInterface
{
    public function store(array $data): void;
}
