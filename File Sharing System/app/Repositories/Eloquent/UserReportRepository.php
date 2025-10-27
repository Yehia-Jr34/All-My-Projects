<?php

namespace App\Repositories\Eloquent;

use App\Models\UserReport;
use App\Repositories\Contracts\UserReportRepositoryInterface;
use Illuminate\Support\Collection;

class UserReportRepository implements UserReportRepositoryInterface
{

    public function get(int $group_id): Collection
    {
        return UserReport::select('action', 'title', 'created_at', 'user_id', 'file_id', 'group_id')
            ->with(['user' => function ($query) {
                $query->select('id', 'name', 'username');
            }])
            ->with(['file' => function ($query) {
                $query->select('id', 'name', 'status');
            }])
            ->with(['group' => function ($query) {
                $query->select('id', 'name', 'description');
            }])
            ->where('group_id', $group_id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function store(array $data): void
    {
        UserReport::insert($data);
    }
}
