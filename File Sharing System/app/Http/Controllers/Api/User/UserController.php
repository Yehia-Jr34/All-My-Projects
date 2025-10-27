<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\DTOs\User\UpdateProfile2DTO;
use App\DTOs\User\UpdateProfileDTO;
use App\DTOs\User\FilteredUserDTO;
use App\Enums\StatusCodeEnum;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\User\FilterUserRequest;
use App\Http\Requests\Api\User\GetProfileRequest;
use App\Http\Requests\Api\User\UpdateProfile2Request;
use App\Http\Requests\Api\User\UpdateProfileRequest;
use App\Http\Resources\User\UserGroupResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;

class UserController extends BaseApiController
{
    public function __construct(
        protected UserService $userService
    )
    {
    }

    public function getProfile(GetProfileRequest $request)
    {
        $user = $this->userService->getProfile();
        return $this->sendSuccess('user profile', [$user], StatusCodeEnum::OK);
    }

    public function update(UpdateProfile2Request $request)
    {
        if ($request->hasFile('image')) {
            $updated_data = UpdateProfile2DTO::fromRequest($request->validated(), $request->image);
            $userDTO = $this->userService->updateWithImage($updated_data);
        } else {
            $userDTO = $this->userService->updateWithoutImage($request->name);
        }

        return $this->sendSuccess('profile updated', [$userDTO], StatusCodeEnum::OK);

    }

    public function filterUsers(FilterUserRequest $request) : JsonResponse {
        if(empty($request->username)){

            return $this->sendSuccess('fetches successfully' , []);
        }
        $users = $this->userService->filterUsers($request->username);

        return $this->sendSuccess('fetches successfully' , $users);
    }

    public function userGroups() : JsonResponse {

        $groups = $this->userService->getUserGroups();

        return $this->sendSuccess('fetched successfully ',($groups));

    }

}
