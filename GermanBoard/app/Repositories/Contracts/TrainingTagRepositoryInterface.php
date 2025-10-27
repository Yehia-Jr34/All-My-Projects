<?php

namespace App\Repositories\Contracts;

interface TrainingTagRepositoryInterface
{
    public function store(array $data): void;
}
