<?php

namespace App\Services;

use App\DTOs\User\UpdateProfile2DTO;
use App\DTOs\User\UpdateProfileDTO;
use App\DTOs\User\FilteredUserDTO;
use App\Models\Group;
use App\Repositories\Contracts\GroupRepositoryInterface;
use App\Repositories\Contracts\UserImageRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected UserImageRepositoryInterface $userImageRepository,
    )
    {
    }

    public function getProfile(): array
    {
        $user = Auth::user();

        $image_path = $this->userImageRepository->getUserImage($user->id)->path;

        $userDTO = FilteredUserDTO::fromUser($user)->toArrayWithImage(url($image_path));
        $userDTO['email'] = $user->email;
        return $userDTO;
    }

    public function updateWithImage (UpdateProfile2DTO $updateProfileDTO): FilteredUserDTO
    {
        $user_email = Auth::user()->email;
        $userDTO = FilteredUserDTO::fromUser(Auth::user());

        $image_name = $userDTO->username . "." . $updateProfileDTO->image->extension();
        $updateProfileDTO->image->move(public_path('images/profile_images'), $image_name);
        $image_path = 'images/profile_images/' . $image_name;

        $image_url = url($this->userImageRepository->update($userDTO->id, $image_path));
        $data = $this->userRepository->updateProfile2($user_email, $updateProfileDTO);
        $data->image_path = $image_url;

        return $data;
    }

    public function updateWithoutImage (string $new_name): FilteredUserDTO
    {
        $user = Auth::user();
        $data = $this->userRepository->updateProfile3($user->email, $new_name);

        $image_path = $this->userImageRepository->getUserImage($user->id)->path;

        $data->image_path = url($image_path);
        return $data;
    }

    public function filterUsers(string $username) : array
    {
        $users = $this->userRepository->filterUsers($username, auth()->user()->username);

        if(empty($users))
            return [] ;

        return FilteredUserDTO::collectionFromModels($users);
    }

    public function getUserGroups(): array
    {
        $user = \auth()->user();

        $groups =  $this->userRepository->getUserGroups($user);

        $owns = $this->userRepository->getUserOwnedGroup($user);

        return  ['groups' => $groups, 'owned' => $owns ];
    }



}
