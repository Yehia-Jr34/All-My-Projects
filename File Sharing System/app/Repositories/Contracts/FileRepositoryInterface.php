<?php

namespace App\Repositories\Contracts;

use App\Models\Files;
use Illuminate\Support\Collection;

interface FileRepositoryInterface
{

    public function create(array $fileData): Files;

    public function getFileById(int $file_id): Files;

    public function lock(int $file_id): void;

    public function lockArray(array $file_ids): void;

    public function unLock(int $file_id): void;

    public function update(int $file_id, string $path): void;

    public function getFileWithVersions(int $file_id): ?Files;

    public function getFilesById(array $file_ids): Collection;

    public function findWithGroup(int $file_id): ?Files;

    public function updateFile(Files $file, array $data): void;

    public function getFileRequests(int $group_id): Collection;

    public function deleteFiles(int $group_id, array $files): void;

    public function getFilesByIdAndLockForUpdate(array $file_ids): Collection;
}
