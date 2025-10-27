<?php

namespace App\Repositories\Contracts;

use App\Models\RefreshToken;
use App\Models\User;

interface RefreshTokenRepositoryInterface
{
    public function createToken(User $user): RefreshToken;
    public function findValidToken(string $token): ?RefreshToken;
    public function deleteToken(string $token): bool;
    public function deleteAllUserTokens(int $userId): bool;
}
