<?php

namespace App\Repositories\Contracts;

use App\Models\Complaint;

interface ComplaintRepositoryInterface
{
    public function getAll(): array;
    public function getById($id): Complaint;
    public function create(array $data): Complaint;
    public function save(Complaint $complaint): void;
}
