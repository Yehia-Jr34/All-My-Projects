<?php

namespace App\Repositories\Contracts;

use App\Models\ResetPasswordCode;

interface ResetPasswordCodeRepositoryInterface
{
    public function store(int $code, int $user_id): void;

    public function findById(int $id): ?ResetPasswordCode;

    public function deleteAllUserCodes(int $user_id): void;
}
