<?php
declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\DTOs\Auth\UserRegistrationDTO;
use App\DTOs\Group\GroupDTO;
use App\DTOs\User\UpdateProfile2DTO;
use App\DTOs\User\UpdateProfileDTO;
use App\DTOs\User\FilteredUserDTO;
use App\DTOs\User\UserDTO;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Collection;

class UserRepository implements UserRepositoryInterface
{

    public function createUser(UserRegistrationDTO $userInformation): FilteredUserDTO
    {
        return FilteredUserDTO::fromUser(User::create($userInformation->toArray()));
    }

    public function getUserDTOByEmail(string $email): FilteredUserDTO|null
    {
        return FilteredUserDTO::fromUser(User::where('email', $email)->first());
    }

    public function getUserByEmail(string $email): User|null
    {
        return User::where('email', $email)->first();
    }

    public function setVerifyAt(int $user_id): void
    {
        User::find($user_id)->update(['email_verified_at' => now()]);
    }

    public function updatePassword(string $newPassword): void
    {
        auth()->user()->update(['password' => $newPassword]);
    }

    public function updateProfile(string $email, UpdateProfileDTO $updateProfileDTO): FilteredUserDTO
    {
        $user = $this->getUserByEmail($email);

        $user->name = $updateProfileDTO->name;
        $user->username = $updateProfileDTO->username;
        $user->email = $updateProfileDTO->email;
        $user->email_verified_at = null;

        $user->save();

        return FilteredUserDTO::fromUser($user);
    }

    public function updateProfile2(string $email, UpdateProfile2DTO $updateProfileDTO): FilteredUserDTO
    {
        $user = $this->getUserByEmail($email);

        $user->name = $updateProfileDTO->name;

        $user->save();

        return FilteredUserDTO::fromUser($user);
    }

    public function filterUsers(string $username, string $current_username): Collection
    {
        return User::where('username', 'like', "%{$username}%")
            ->where('username', '!=', $current_username)
            ->get(['id', 'username', 'name']);
    }

    public function getUserGroups(User $user): array
    {
        return GroupDTO::collection($user->groups()->with('owner')->get());
    }

    public function getUserById(int $user_id): UserDTO
    {
        return UserDTO::fromUserRepo(User::find($user_id));
    }

    public function getUserOwnedGroup($user): array
    {
        return GroupDTO::collection($user->owner()->get());
    }

    public function getUserById2(int $user_id): User
    {
        return User::find($user_id);
    }

    public function updateProfile3(string $email, string $new_name): FilteredUserDTO
    {
        $user = $this->getUserByEmail($email);

        $user->name = $new_name;
        $user->save();

        return FilteredUserDTO::fromUser($user);
    }
}
