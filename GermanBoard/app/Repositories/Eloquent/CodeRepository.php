<?php

namespace App\Repositories\Eloquent;

use App\Models\Code;
use App\Repositories\Contracts\CodeRepositoryInterface;

class CodeRepository implements CodeRepositoryInterface
{
    public function store(string $phone_number, int $code): void
    {
        Code::create([
            'trainee_phone_number' => $phone_number,
            'code' => $code,
            'expired_at' => now()->addMinutes(5),
        ]);
    }

    public function getCode(string $phone_number): ?Code
    {
        return Code::select('code', 'expired_at')
            ->where('trainee_phone_number', $phone_number)
            ->orderByDesc('created_at')
            ->first();
    }

    public function deleteAllUserCodes(string $phone_number): void
    {
        Code::where('trainee_phone_number', $phone_number)->delete();
    }
}
