<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Code\CodeDTO;
use App\Models\Code;
use App\Repositories\Contracts\CodeRepositoryInterface;

class CodeRepository implements CodeRepositoryInterface
{

    public function getCodeByUserId(int $user_id): CodeDTO|null
    {
        return CodeDTO::fromCode(Code::where('user_id', $user_id)
            ->orderByDesc('created_at')
            ->first());
    }

    public function storeCode(int $code, int $user_id)
    {
        Code::create([
            'code' => $code,
            'user_id' => $user_id,
            'expired_at' => now()->addMinutes(5)
        ]);
    }

    public function deleteAllUserCodes(int $user_id): void
    {
        Code::where('user_id' , $user_id)->delete();
    }
}
