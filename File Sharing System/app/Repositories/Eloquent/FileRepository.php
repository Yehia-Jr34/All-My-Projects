<?php

namespace App\Repositories\Eloquent;

use App\Models\Files;
use App\Repositories\Contracts\FileRepositoryInterface;
use Illuminate\Support\Collection;

class FileRepository implements FileRepositoryInterface
{

    public function create(array $fileData): Files
    {
        return Files::create($fileData);
    }

    public function getFileById(int $file_id): Files
    {
        return  Files::with('group')->find($file_id);
    }

    public function lock(int $file_id): void
    {
        Files::find($file_id)->update(['status' => false]);
    }

    public function unLock(int $file_id): void
    {
        Files::find($file_id)->update(['status' => true]);
    }

    public function update(int $file_id, string $path): void
    {
        Files::find($file_id)->update(['path' => $path]);
    }

    public function getFileWithVersions(int $file_id): ?Files
    {
        return Files::with([
            'group:id,created_by',
            'versions' => function ($q) {

                $q->select(['id', 'file_id', 'created_at', 'user_id', 'size']);
            },
            'versions.user:id,name,email,username,email_verified_at',

        ])->find($file_id);
    }

    public function lockArray(array $file_ids): void
    {
        Files::whereIn('id', $file_ids)->update(['status' => false]);
    }

    public function getFilesById(array $file_ids): Collection
    {
        return Files::with('group')->whereIn('id', $file_ids)->get();
    }

    public function findWithGroup(int $file_id): ?Files
    {
        return Files::with('group')->find($file_id);
    }

    public function updateFile(Files $file, array $data): void
    {
        $file->update($data);
    }

    public function getFileRequests(int $group_id): Collection
    {
        return Files::with('versions.user')
            ->where('admin_group_approve', 'pending')
            ->where('group_id', $group_id)
            ->get();
    }

    public function deleteFiles(int $group_id, array $files): void
    {
        Files::where('group_id', $group_id)
            ->whereIn('id', $files)
            ->each(function ($file) {
                $file->versions()->delete();
                $file->delete();
            });
    }

    public function getFilesByIdAndLockForUpdate(array $file_ids): Collection
    {
        return Files::whereIn('id', $file_ids)->lockForUpdate()->get();
    }
}
