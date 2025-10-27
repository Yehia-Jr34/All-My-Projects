<?php

namespace App\Repositories\Contracts;

use App\Models\FileVersion;
use Illuminate\Support\Collection;

interface FileVersionRepositoryInterface
{
    public function create(array $data): FileVersion;

    public function getLastVersionToFile(int $id): ?FileVersion;

    public function find(int $id): ?FileVersion;

    public function update(FileVersion $fileVersion, array $data): void;

    public function findWithGroup(int $file_version_id): ?FileVersion;

    public function insert(array $array): void;

    public function myUnlocks(int $user_id, int $group_id): Collection;

    public function delete(int $id): bool;

    public function getLastUploadedVersionToFile(int $file_id): ?FileVersion;

    public function getFileVersionsWithDiff(int $file_id): Collection;
}
