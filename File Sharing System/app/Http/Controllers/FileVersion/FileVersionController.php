<?php

namespace App\Http\Controllers\FileVersion;

use App\Enums\StatusCodeEnum;
use App\Http\Controllers\Api\BaseApiController;
use App\Services\FileVersion\FileVersionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class FileVersionController extends BaseApiController
{
    public function __construct(
        protected FileVersionService $fileVersionService
    )
    {
    }

    public function download(int $file_version_id)
    {
        try {

            $path = $this->fileVersionService->download($file_version_id);

            $fileFullPath = storage_path("app/public/{$path}");

            return response()->download($fileFullPath);

        } catch (\DomainException $domainException) {

            return $this->sendError($domainException->getMessage(), StatusCodeEnum::BAD_REQUEST);

        } catch (\Exception $exception) {

            return $this->sendError($exception->getMessage(), StatusCodeEnum::INTERNAL_SERVER_ERROR);

        }
    }

}
