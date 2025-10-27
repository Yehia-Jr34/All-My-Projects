<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface FileReportRepositoryInterface
{
    public function getFileReports(int $file_id): Collection;

    public function addAction(int $file_id, int $user_id, string $operation): void;
}
