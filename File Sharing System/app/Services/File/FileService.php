<?php

namespace App\Services\File;

use App\Enums\FilesVersionsStatusEnum;
use App\Enums\StatusCodeEnum;
use App\Events\LockedFileEvent;
use App\Events\NewFileEvent;
use App\Events\UnlockedFileEvent;
use App\Models\Files;
use App\Models\FileVersion;
use App\Models\Group;
use App\Models\User;
use App\Repositories\Contracts\AddFileNotificationRepositoryInterface;
use App\Repositories\Contracts\AddFileNotificationResponseRepositoryInterface;
use App\Repositories\Contracts\FileReportRepositoryInterface;
use App\Repositories\Contracts\FileRepositoryInterface;
use App\Repositories\Contracts\FileVersionRepositoryInterface;
use App\Repositories\Contracts\GroupRepositoryInterface;
use App\Repositories\Contracts\LockedFileNotificationRepositoryInterface;
use App\Repositories\Contracts\UnlockedFileNotificationRepositoryInterface;
use App\Repositories\Contracts\UserGroupRepositoryInterface;
use App\Repositories\Contracts\UserReportRepositoryInterface;
use Faker\Core\File;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mockery\Exception;

class FileService
{
    public function __construct(
        protected FileRepositoryInterface                        $fileRepository,
        protected GroupRepositoryInterface                       $groupRepository,
        protected FileVersionRepositoryInterface                 $fileVersionRepository,
        protected AddFileNotificationRepositoryInterface         $addFileNotificationRepository,
        protected AddFileNotificationResponseRepositoryInterface $addFileNotificationResponseRepository,
        protected UserGroupRepositoryInterface                   $userGroupRepository,
        protected LockedFileNotificationRepositoryInterface      $lockedFileNotificationRepository,
        protected UnlockedFileNotificationRepositoryInterface    $unlockedFileNotificationRepository,
        protected FileReportRepositoryInterface                  $fileReportRepository,
        protected UserReportRepositoryInterface                  $userReportRepository
    ) {}

    public function create(array $data)
    {
        $user = \auth()->user();

        $group = $this->groupRepository->getGroupById($data['group_id']);

        $admin_group_approve = FilesVersionsStatusEnum::PENDING->value;


        if ($user->cannot('isMember', $group) && $user->cannot('ownsGroup', $group)) {

            throw new \DomainException('You are not authorized to manage this group.');
        }

        if ($user->can('ownsGroup', $group)) {

            $admin_group_approve = FilesVersionsStatusEnum::ACCEPTED->value;
        }

        // store file in folder files/groupName/fileName/{filename}
        $uploadedFile = $data['file'];

        $content = file_get_contents($data['file']->getPathname());

        // Check if the content is valid UTF-8 (suitable for text-based files).
        if (!mb_check_encoding($content, 'UTF-8')) {

            throw new \DomainException(
                'The uploaded file must contain valid text content (e.g., UTF-8 encoding).',
            );
        }

        // Generate a unique filename to avoid conflicts
        $uniqueFileName = uniqid() . '_' . $uploadedFile->getClientOriginalName();

        // Define storage path
        $storagePath = "files/{$group->name}/{$uploadedFile->getClientOriginalName()}";

        $storedFilePath = $uploadedFile->storeAs($storagePath, $uniqueFileName, 'public');

        $fileData = [
            'group_id' => $group->id,
            'created_by' => $user->id,
            'name' => $uploadedFile->getClientOriginalName(),
            'status' => true,
            'admin_group_approve' => $admin_group_approve,
            'approved_at' => $admin_group_approve === FilesVersionsStatusEnum::PENDING->value ? null : now(),
            'size' => $uploadedFile->getSize()
        ];

        //create file
        $file = $this->fileRepository->create($fileData);

        $this->fileVersionRepository->create([
            'user_id' => $user->id,
            'file_id' => $file->id,
            'size' => $uploadedFile->getSize(),
            'path' => $storedFilePath,
            'diff_path' => null,
        ]);

        if ($user->cannot('ownsGroup', $group)) {
            //store add file request in add file notification table
            $data = [
                'file_id' => $file->id,
                'user_id' => $group->created_by,
                'group_id' => $group->id,
                'notification_text' => $user->name . " requested to add the file : " . $uploadedFile->getClientOriginalName() . " to the group : " . $group->name,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $this->addFileNotificationRepository->store($data);
            event(new NewFileEvent($data['user_id'], $data));
        }





        $user_report_data = collect($file->id)
            ->map(function ($item) use ($group, $file, $user) {
                return [
                    'user_id' => $user->id,
                    'file_id' => $item,
                    'group_id' => $group->id,
                    'title' => 'create',
                    'action' => 'added',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });

        $this->userReportRepository->store($user_report_data->toArray());
    }


    public function lock(array $data): void
    {
        DB::transaction(function () use ($data) {
            // Lock the files for update to prevent concurrent locks
            $files = $this->fileRepository->getFilesByIdAndLockForUpdate($data['file_ids']);

            $user = auth()->user();

            $fileVersions = [];
            if ($files->isEmpty()) {
                throw new \DomainException('no files found');
            }

            $ids = $this->userGroupRepository->getMembersIds($files[0]->group->id);

            foreach ($files as $file) {

                $this->ensureCanLockUnlockFile($file->group, $user);

                // Double check if file is already locked
                if (!$file->status) {
                    throw new \DomainException("file {$file->name} already locked");
                }

                $this->fileRepository->lock($file->id);

                if ($file->admin_group_approve !== FilesVersionsStatusEnum::ACCEPTED->value) {
                    throw new \DomainException("file {$file->name} not approved");
                }

                $fileVersions[] = [
                    'user_id' => $user->id,
                    'file_id' => $file->id,
                    'size' => $file->size,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $notification_data = collect($ids)
                    ->map(function ($item) use ($file, $user) {
                        return [
                            'user_id' => $item->user_id,
                            'notification_text' => "the file : " . $file->name . " in the group : " . $file->group->name . " has been locked by the user : " . $user->name,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    });

                $notification_data[] = [
                    'user_id' => $file->group->created_by,
                    'notification_text' => "the file : " . $file->name . " in the group : " . $file->group->name . " has been locked ",
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $this->lockedFileNotificationRepository->store($notification_data->toArray());

                foreach ($notification_data as $notification) {
                    event(new LockedFileEvent($notification['user_id'], $notification));
                }

                $this->fileReportRepository->addAction($file->id, $user->id, 'lock');

                $user_report_data = collect($files)
                    ->map(function ($item) use ($file, $user) {
                        return [
                            'user_id' => $user->id,
                            'file_id' => $item->id,
                            'group_id' => $file->group->id,
                            'title' => 'lock',
                            'action' => 'locked',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    });

                $this->userReportRepository->store($user_report_data->toArray());
            }


            $this->fileVersionRepository->insert($fileVersions);
        });
    }

    public function unlock(array $data): string
    {
        return DB::transaction(function () use ($data) {
            $file = $this->fileRepository->getFileById($data['file_id']);
            $group = $file->group;

            if ($file->name !== $data['file']->getClientOriginalName()) {
                throw new \DomainException('must be the same extension and name');
            }

            if ($file->status) {
                throw new \DomainException("file {$file->name} already unlocked");
            }

            $newContent = file_get_contents($data['file']->getPathname());
            // Check if the content is valid UTF-8
            if (!mb_check_encoding($newContent, 'UTF-8')) {
                throw new \DomainException(
                    'The uploaded file must contain valid text content (e.g., UTF-8 encoding).',
                );
            }

            $file_version = $this->fileVersionRepository->getLastVersionToFile($file->id);

            $last_uploaded_file_version = $this->fileVersionRepository->getLastUploadedVersionToFile($file->id);



            // Fix the path construction
            $currentVersionPath = storage_path('app/public/' . $last_uploaded_file_version->path);

            // Check if file exists before reading
            if (!file_exists($currentVersionPath)) {
                throw new \DomainException('Previous version file not found');
            }

            $currentContent = file_get_contents($currentVersionPath);

            if ($file->status === false) {
                throw new \DomainException('this file is not locked');
            }

            $user = \auth()->user();
            $this->ensureOwnFileVersion($file_version, $user);

            // Compare contents
            if ($currentContent === $newContent) {
                // Unlock the file without creating new version
                $this->fileRepository->unLock($file->id);
                $this->fileVersionRepository->delete($file_version->id);
                return 'file content is unchanged. File has been unlocked.';
            }

            // Generate diff data
            $diffData = $this->generateDiffData($currentContent, $newContent);

            // Store diff data as JSON file
            $diffFileName = uniqid() . '_diff.json';
            $diffPath = "files/{$group->name}/{$file->name}/diffs/{$diffFileName}";

            Storage::disk('public')->put(
                "{$diffPath}",
                json_encode($diffData, JSON_PRETTY_PRINT),

            );

            // Store new version with diff path
            $uploadedFile = $data['file'];
            $uniqueFileName = uniqid() . '_' . $uploadedFile->getClientOriginalName();
            $storagePath = "files/{$group->name}/{$file->name}";
            $storedFilePath = $uploadedFile->storeAs($storagePath, $uniqueFileName, 'public');

            $data = [
                'path' => $storedFilePath,
                'diff_path' => $diffPath,
            ];

            $this->fileVersionRepository->update($file_version, $data);
            $this->fileRepository->unLock($file->id);

            $ids = $this->userGroupRepository->getMembersIds($file->group->id);

            $notification_data = collect($ids)
                ->map(function ($item) use ($file, $user) {
                    return [
                        'user_id' => $item->user_id,
                        'notification_text' => "the file : " . $file->name . " in the group : " . $file->group->name . " has been unlocked ",
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                });

            $notification_data[] = [
                'user_id' => $group->created_by,
                'notification_text' => "the file : " . $file->name . " in the group : " . $file->group->name . " has been unlocked ",
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $this->unlockedFileNotificationRepository->store($notification_data->toArray());

            foreach ($notification_data as $notification) {
                event(new UnlockedFileEvent($notification['user_id'], $notification));
            }

            $this->fileReportRepository->addAction($file->id, $user->id, 'lock');

            $user_report_data = collect($file->id)
                ->map(function ($item) use ($group, $file, $user) {
                    return [
                        'user_id' => $user->id,
                        'file_id' => $item,
                        'group_id' => $group->id,
                        'title' => 'unlock',
                        'action' => 'unlocked',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                });

            $this->userReportRepository->store($user_report_data->toArray());
            return 'file content is changed. File has been unlocked.';
        });
    }

    public function showWithfiles(int $file_id): Files
    {
        $file = $this->fileRepository->getFileWithVersions($file_id);

        if (!$file) {

            throw new \DomainException('file not found', StatusCodeEnum::NOT_FOUND->value);
        }

        $group = $file->group;

        $user = auth()->user();

        $this->ensureUserCanManageGroup($group, $user);

        return $file;
    }

    public function accept(int $file_id): void
    {
        DB::transaction(function () use ($file_id) {

            $file = $this->fileRepository->findWithGroup($file_id);

            if (!$file) {

                throw new \DomainException('not found');
            }

            if ($file->admin_group_approve !== FilesVersionsStatusEnum::PENDING->value) {

                throw new \DomainException('Already been handled');
            }

            $group = $file->group;

            $user = auth()->user();

            $this->ensureUserCanAcceptFile($group, $user);

            $this->fileRepository->updateFile($file, [
                'admin_group_approve' => FilesVersionsStatusEnum::ACCEPTED->value,
                'approved_at' => now()
            ]);

            //set add file notification status to accepted
            $addFileNotification = $this->addFileNotificationRepository->get($user->id, $group->id, $file_id);
            $this->addFileNotificationRepository->fileAccepted($addFileNotification);

            //store accepted add file response notification
            $data = [
                'file_id' => $file_id,
                'user_id' => $file->created_by,
                'group_id' => $group->id,
                'notification_text' => $user->name . " has accepted you request to add the file : " . $file->name,
                'created_at' => now(),
                'updated_at' => now()
            ];
            $this->addFileNotificationResponseRepository->store($data);
        });
    }

    public function reject(int $file_id): void
    {

        DB::transaction(function () use ($file_id) {

            $file = $this->fileRepository->findWithGroup($file_id);

            if (!$file) {

                throw new \DomainException('not found');
            }

            if ($file->admin_group_approve !== FilesVersionsStatusEnum::PENDING->value) {

                throw new \DomainException('Already been handled');
            }

            $group = $file->group;

            $user = auth()->user();

            $this->ensureUserCanAcceptFile($group, $user);

            $this->fileRepository->updateFile($file, ['admin_group_approve' => FilesVersionsStatusEnum::REJECTED->value]);

            //set add file notification status to rejected
            $addFileNotification = $this->addFileNotificationRepository->get($user->id, $group->id, $file_id);
            $this->addFileNotificationRepository->fileRejected($addFileNotification);

            //store rejected add file response notification
            $data = [
                'file_id' => $file_id,
                'user_id' => $file->created_by,
                'group_id' => $group->id,
                'notification_text' => $user->name . " has rejected you request to add the file : " . $file->name,
                'created_at' => now(),
                'updated_at' => now()
            ];
            $this->addFileNotificationResponseRepository->store($data);
        });
    }

    public function myUnlocks(int $group_id): Collection
    {
        $user = auth()->user();

        $files = $this->fileVersionRepository->myUnlocks($user->id, $group_id);

        return $files;
    }

    public function fileRequests(int $group_id): Collection
    {
        $user = auth()->user();

        $group = $this->groupRepository->find($group_id);

        if (!$group) {

            throw new \DomainException('group not found');
        }

        $this->ensureUserCanManageGroup($group, $user);

        $files = $this->fileRepository->getFileRequests($group_id);

        return $files;
    }

    // Helper Methods

    public function ensureUserCanManageGroup(Group $group, User $user): void
    {

        if (!$user->can('ownsGroup', $group)) {

            throw new \DomainException('You are not authorized to manage this group.');
        }
    }

    public function ensureCanLockUnlockFile(Group $group, User $user): void
    {

        if (!$user->can('ownsGroup', $group) && !$user->can('isAdmin', $group) && !$user->can('isMember', $group)) {

            throw new \DomainException('You are not authorized to manage this group.');
        }
    }

    public function ensureOwnFileVersion(FileVersion $fileVersion, User $user): void
    {

        if (!$user->can('ownFileVersion', $fileVersion)) {

            throw new \DomainException('You are not authorized to manage this file.');
        }
    }

    public function ensureUserCanAcceptFile(Group $group, User $user): void
    {

        if (!$user->can('ownsGroup', $group)) {

            throw new \DomainException('You are not authorized to manage this file.');
        }
    }

    public function deleteFile(int $group_id, array $files): string
    {
        $user_id = Auth::user()->id;
        $group = $this->groupRepository->find($group_id);
        $group_created_by = $group->created_by;
        if ($this->userIsAnAdmin($group_created_by, $user_id)) {
            $this->fileRepository->deleteFiles($group_id, $files);
            return "file deleted";
        }
        throw new Exception("you are not authorized tho process this action");
    }

    private function userIsAnAdmin(int $group_created_by, int $user_id): bool
    {
        return $group_created_by == $user_id;
    }

    public function getReport(int $file_id): Collection
    {
        try {
            $data = $this->fileReportRepository->getFileReports($file_id);
            return $data;
        } catch (\Exception) {
            throw new \DomainException('something went wrong');
        }
    }

    public function getVersionHistory(int $fileId): array
    {

        $user = auth()->user();
        $group_id = $this->fileRepository->getFileById($fileId)->group_id;
        $group = $this->groupRepository->find($group_id);

        if (!$user->can('ownsGroup', $group) && !$user->can('isAdmin', $group) && !$user->can('isMember', $group)) {

            throw new \DomainException('You are not authorized to manage this group.');
        }

        $versions = $this->fileVersionRepository->getFileVersionsWithDiff($fileId);
        $history = [];



        foreach ($versions as $version) {
            if ($version->diff_path) {
                $diffContent = Storage::disk('public')->get($version->diff_path);
                $diff = json_decode($diffContent, true);
                $history[] = [
                    'id' => $version->id,
                    'total_rows' => $diff['total_rows'] ?? 0,
                    'added_rows' => $diff['added_rows'] ?? [],
                    'deleted_rows' => $diff['deleted_rows'] ?? [],
                    'created_at' => $version->created_at,
                    'created_by' => $version->user->name
                ];
            } else {
                $history[] = [
                    'id' => $version->id,
                    'total_rows' => count(explode("\n", Storage::disk('public')->get($version->path))),
                    'added_rows' => [],
                    'deleted_rows' => [],
                    'created_at' => $version->created_at,
                    'created_by' => $version->user->name
                ];
            }
        }

        return ($history); // Most recent first
    }

    private function generateDiffData(string $oldContent, string $newContent): array
    {
        $oldLines = explode("\n", $oldContent);
        $newLines = explode("\n", $newContent);
        $added = array_diff($newLines, $oldLines);
        $deleted = array_diff($oldLines, $newLines);
        return [
            'total_rows' => count($newLines),
            'added_rows' => $added,
            'deleted_rows' => $deleted
        ];
    }
}
