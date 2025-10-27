<?php
declare(strict_types=1);

namespace App\Repositories\Contracts;


use App\DTOs\Auth\UserRegistrationDTO;
use App\DTOs\User\UpdateProfile2DTO;
use App\DTOs\User\UpdateProfileDTO;
use App\DTOs\User\FilteredUserDTO;
use App\DTOs\User\UserDTO;
use App\Models\User;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    public function createUser(UserRegistrationDTO $userInformation): FilteredUserDTO;

    public function getUserDTOByEmail(string $email): FilteredUserDTO|null;

    public function getUserByEmail(string $email): User|null;

    public function setVerifyAt(int $user_id): void;

    public function updatePassword(string $newPassword): void;

    public function updateProfile(string $email, UpdateProfileDTO $updateProfileDTO): FilteredUserDTO;

    public function updateProfile2(string $email, UpdateProfile2DTO $updateProfileDTO): FilteredUserDTO;
    public function updateProfile3(string $email, string $new_name): FilteredUserDTO;

    public function filterUsers(string $username, string $current_username): Collection;

    public function getUserGroups(User $user): array;

    public function getUserById(int $user_id): UserDTO;

    public function getUserById2(int $user_id): User;

    public function getUserOwnedGroup($user): array;

}
