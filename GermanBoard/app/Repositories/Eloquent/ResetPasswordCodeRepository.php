<?php

namespace App\Repositories\Eloquent;

use App\Models\ResetPasswordCode;
use App\Repositories\Contracts\ResetPasswordCodeRepositoryInterface;

class ResetPasswordCodeRepository implements ResetPasswordCodeRepositoryInterface
{
    public function store(int $code, int $user_id): void
    {
        ResetPasswordCode::create([
            'code' => $code,
            'user_id' => $user_id,
            'expired_at' => now()->addMinutes(5),
        ]);
    }

    public function findById(int $id): ?ResetPasswordCode
    {
        return ResetPasswordCode::select('code', 'expired_at')
            ->where('user_id', $id)
            ->orderByDesc('created_at')
            ->first();
    }

    public function deleteAllUserCodes(int $user_id): void
    {
        ResetPasswordCode::where('user_id', $user_id)->delete();
    }
}
