<?php

namespace App\Repositories\Eloquent;

use App\Models\RefreshToken;
use App\Models\User;
use App\Repositories\Contracts\RefreshTokenRepositoryInterface;
use Illuminate\Support\Str;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    public function createToken(User $user): RefreshToken
    {
        return RefreshToken::create([
            'user_id' => $user->id,
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
        ]);
    }

    public function findValidToken(string $token): ?RefreshToken
    {
        return RefreshToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();
    }

    public function deleteToken(string $token): bool
    {
        return (bool) RefreshToken::where('token', $token)->delete();
    }

    public function deleteAllUserTokens(int $userId): bool
    {
        return (bool) RefreshToken::where('user_id', $userId)->delete();
    }
}
