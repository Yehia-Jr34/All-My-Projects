<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function createUser(array $data): ?User
    {
        return User::create($data);
    }

    public function getUserById(int $id): ?User
    {
        return User::find($id);
    }

    public function getUserByEmail(string $email): ?User
    {
        return User::select('id', 'email', 'email_verified_at')
            ->where('email', $email)
            ->first();
    }

    public function getUserByPhoneNumber(string $phone_number): ?User
    {
        return User::select('id', 'email', 'email_verified_at')
            ->where('phone_number', $phone_number)
            ->first();
    }

    public function getUserCredentialByEmail(string $email): ?User
    {
        return User::select('id', 'email', 'password', 'email_verified_at', 'fcm_token')
            ->where('email', $email)
            ->first();
    }

    public function verify(int $user_id): void
    {
        $user = User::find($user_id);
        $user->email_verified_at = now();
        $user->save();
    }

    public function updatePassword(int $user_id, string $password): void
    {
        User::find($user_id)->update(['password' => $password]);
    }

    public function getTokensByIds(array $ids): array
    {
        return User::whereIn('id', $ids)
            ->pluck('fcm_token')
            ->filter()
            ->map(fn($token) => ['fcm_token' => (string) $token])
            ->toArray();
    }

    public function updateDeviceToken(int $user_id, string $device_token): void
    {
        User::find($user_id)->update(['fcm_token' => $device_token]);
    }

    public function getDeviceTokens(array $userIds): array
    {
        return User::whereIn('id', $userIds)
            ->whereNotNull('fcm_token')
            ->pluck('fcm_token')
            ->toArray();
    }
}
