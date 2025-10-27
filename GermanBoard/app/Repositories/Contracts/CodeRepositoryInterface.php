<?php

namespace App\Repositories\Contracts;

use App\Models\Code;

interface CodeRepositoryInterface
{
    public function store(string $phone_number, int $code): void;

    public function getCode(string $phone_number): ?Code;

    public function deleteAllUserCodes(string $phone_number): void;
}
