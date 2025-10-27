<?php

namespace App\Repositories\Eloquent;

use App\Models\FileReport;
use App\Repositories\Contracts\FileReportRepositoryInterface;
use Illuminate\Support\Collection;

class FileReportRepository implements FileReportRepositoryInterface
{

    public function getFileReports(int $file_id): Collection
    {
        return FileReport::select('operation', 'created_at', 'user_id', 'file_id')
        ->with(['user' => function ($query) {
            $query->select('id', 'name', 'username');
        }])
            ->with(['file' => function ($query) {
                $query->select('id','name');
            }])
            ->where('file_id', $file_id)
            ->get();
    }

    public function addAction(int $file_id, int $user_id, string $operation): void
    {
        FileReport::create([
            'file_id' => $file_id,
            'user_id' => $user_id,
            'operation' => $operation
        ]);
    }
}
