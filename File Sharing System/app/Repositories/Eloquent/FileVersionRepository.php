<?php

namespace App\Repositories\Eloquent;

use App\Enums\FilesVersionsStatusEnum;
use App\Models\FileVersion;
use App\Repositories\Contracts\FileVersionRepositoryInterface;
use Illuminate\Support\Collection;

class FileVersionRepository implements FileVersionRepositoryInterface
{

    public function create(array $data): FileVersion
    {
        return FileVersion::create($data);
    }

    public function getLastVersionToFile(int $file_id): ?FileVersion
    {
        return FileVersion::where('file_id', $file_id)
            ->orderBy('id', 'desc')->first();
    }

    public function update(FileVersion $fileVersion, array $data): void
    {
        $fileVersion->update($data);
    }

    public function find(int $id): ?FileVersion
    {
        return FileVersion::find($id);
    }

    public function findWithGroup(int $file_version_id): ?FileVersion
    {
        return FileVersion::with('file.group')->find($file_version_id);
    }

    public function insert(array $data): void
    {
        FileVersion::insert($data);
    }

    public function myUnlocks(int $user_id, int $group_id): Collection
    {
        return FileVersion::with(['file'])
            ->where('user_id', $user_id)
            ->where('path', null)
            ->whereHas('file', function ($query) use ($group_id) {
                $query->where('group_id', $group_id);
            })
            ->get();
    }

    public function delete(int $id): bool
    {
        return FileVersion::where('id', $id)->delete();
    }

    public function getLastUploadedVersionToFile(int $file_id): ?FileVersion
    {
        return FileVersion::where('file_id', $file_id)->where('path', '!=', null)
            ->orderBy('id', 'desc')->first();
    }

    public function getFileVersionsWithDiff(int $file_id): Collection
    {
        return FileVersion::where('file_id', $file_id)->get();
    }
}
