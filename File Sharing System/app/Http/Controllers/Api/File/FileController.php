<?php

namespace App\Http\Controllers\Api\File;

use App\DTOs\Group\DeleteFileDTO;
use App\Enums\StatusCodeEnum;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Aoi\Files\FileRequestsRequest;
use App\Http\Requests\Api\File\CreateFileRequest;
use App\Http\Requests\Api\File\GetHistoryTracingRequest;
use App\Http\Requests\Api\File\LockFileRequest;
use App\Http\Requests\Api\File\UnLockFileRequest;
use App\Http\Requests\Api\Group\DeleteFileRequest;
use App\Http\Resources\Api\Files\MyUnlocksResourc;
use App\Services\File\FileService;
use Illuminate\Http\JsonResponse;
use Mockery\Exception;

class FileController extends BaseApiController
{
    public function __construct(
        protected FileService $fileService
    ) {}

    public function accept(int $file_id): JsonResponse
    {

        try {

            $this->fileService->accept($file_id);

            return $this->sendSuccess('accepted successfully', []);
        } catch (\DomainException $domainException) {

            return $this->sendError($domainException->getMessage(), StatusCodeEnum::BAD_REQUEST);
        } catch (\Exception $exception) {

            return $this->sendError($exception->getMessage(), StatusCodeEnum::INTERNAL_SERVER_ERROR);
        }
    }

    public function reject(int $file_id): JsonResponse
    {

        try {

            $this->fileService->reject($file_id);

            return $this->sendSuccess('rejected successfully', []);
        } catch (\DomainException $domainException) {

            return $this->sendError($domainException->getMessage(), StatusCodeEnum::BAD_REQUEST);
        } catch (\Exception $exception) {

            return $this->sendError($exception->getMessage(), StatusCodeEnum::INTERNAL_SERVER_ERROR);
        }
    }

    public function create(CreateFileRequest $request): JsonResponse
    {


        $this->fileService->create($request->validated());

        return $this->sendSuccess('created successfully', []);
    }

    public function lock(LockFileRequest $request): JsonResponse
    {


        $this->fileService->lock($request->validated());

        return $this->sendSuccess('locked successfully', []);
    }

    public function unLock(UnLockFileRequest $request): JsonResponse
    {
        try {

            $message = $this->fileService->unlock($request->validated());

            return $this->sendSuccess($message, []);
        } catch (\DomainException $domainException) {

            return $this->sendError($domainException->getMessage(), StatusCodeEnum::BAD_REQUEST);
        } catch (\Exception $exception) {

            return $this->sendError($exception->getMessage(), StatusCodeEnum::INTERNAL_SERVER_ERROR);
        }
    }

    public function showWithfilesAdmin(int $file_id): JsonResponse
    {
        try {

            $file = $this->fileService->showWithfiles($file_id);

            return $this->sendSuccess("fetched successfully", $file);
        } catch (\DomainException $domainException) {

            return $this->sendError($domainException->getMessage(), StatusCodeEnum::BAD_REQUEST);
        } catch (\Exception $exception) {

            return $this->sendError($exception->getMessage(), StatusCodeEnum::INTERNAL_SERVER_ERROR);
        }
    }

    public function getUnlocks(int $group_id): JsonResponse
    {

        try {

            $files = $this->fileService->myUnlocks($group_id);

            return $this->sendSuccess("fetched successfully", MyUnlocksResourc::collection($files));
        } catch (\DomainException $domainException) {

            return $this->sendError($domainException->getMessage(), StatusCodeEnum::BAD_REQUEST);
        } catch (\Exception $exception) {

            return $this->sendError($exception->getMessage(), StatusCodeEnum::INTERNAL_SERVER_ERROR);
        }
    }

    public function fileRequests(FileRequestsRequest $request): JsonResponse
    {

        try {

            $files = $this->fileService->fileRequests($request->validated()['group_id']);

            return $this->sendSuccess("fetched successfully", $files);
        } catch (\DomainException $domainException) {

            return $this->sendError($domainException->getMessage(), StatusCodeEnum::BAD_REQUEST);
        } catch (\Exception $exception) {

            return $this->sendError($exception->getMessage(), StatusCodeEnum::INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteFile(DeleteFileRequest $request): JsonResponse
    {
        try {
            $delete_file_DTO = DeleteFileDTO::fromArray($request->validated());
            $data = $this->fileService->deleteFile($delete_file_DTO->group_id, $delete_file_DTO->files);
            return $this->sendSuccess($data, []);
        } catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), StatusCodeEnum::BAD_REQUEST);
        }
    }

    public function getFileReport(int $file_id): JsonResponse
    {
        try {
            $data = $this->fileService->getReport($file_id);
            return $this->sendSuccess("fetched successfully", $data);
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage(), StatusCodeEnum::BAD_REQUEST);
        }
    }

    public function getHistory(GetHistoryTracingRequest $request): JsonResponse
    {

        $data = $this->fileService->getVersionHistory($request->validated()['file_id']);
        return $this->sendSuccess("fetched successfully", $data);
    }
}
