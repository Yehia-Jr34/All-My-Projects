<?php

namespace App\Repositories\Contracts;

interface RoleRepositoryInterface
{
    public function create(string $name): void;
    public function storeOrFind(string $role): string;
    public function getAll(): array;
}
