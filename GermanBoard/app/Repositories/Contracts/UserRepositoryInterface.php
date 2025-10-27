<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    public function createUser(array $data): ?User;

    public function getUserById(int $id): ?User;

    public function getUserByEmail(string $email): ?User;

    public function getUserCredentialByEmail(string $email): ?User;

    public function verify(int $user_id): void;

    public function updatePassword(int $user_id, string $password): void;

    public function getUserByPhoneNumber(string $phone_number): ?User;

    public function getTokensByIds(array $ids): array;

    public function updateDeviceToken(int $user_id, string $device_token): void;

    public function getDeviceTokens(array $userIds): array;
}
