<?php

namespace App\Services\FileVersion;

use App\Enums\FilesVersionsStatusEnum;
use App\Models\Group;
use App\Models\User;
use App\Repositories\Contracts\FileRepositoryInterface;
use App\Repositories\Contracts\FileVersionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FileVersionService
{
    public function __construct(
        protected FileVersionRepositoryInterface $fileVersionRepository,
        protected FileRepositoryInterface $fileRepository
    )
    {
    }


    public function download(int $file_version_id) : string
    {

        $file_version = $this->fileVersionRepository->findWithGroup($file_version_id);

        if(!$file_version){
            throw  new  \DomainException('not found');
        }

        $group = $file_version->file->group;

        $this->ensureUserCanDownloadFile($group , auth()->user());

        return $file_version->path;
    }

    // Helper Methods

    public function ensureUserCanDownloadFile(Group $group , User $user): void
    {

        if (!$user->can('isMember', $group) && !$user->can('isAdmin', $group) && !$user->can('ownsGroup', $group)) {

            throw new \DomainException('You are not authorized to manage this file.');

        }
    }

}
