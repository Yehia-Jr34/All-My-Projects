<?php

namespace App\Repositories\Contracts;

interface PermissionRepositoryInterface
{
    public function create(string $permission_name): void;

    public function getAll(): array;
}
