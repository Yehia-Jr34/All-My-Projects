<?php

namespace App\Http\Controllers\Api\Group;

use App\DTOs\Group\CreateGroupDTO;
use App\DTOs\Group\DeleteFileDTO;
use App\DTOs\Group\EditGroupNameDTO;
use App\DTOs\Group\GroupDTO;
use App\DTOs\Group\RemoveMembersDTO;
use App\Enums\StatusCodeEnum;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Group\CreateGroupRequest;
use App\Http\Requests\Api\Group\DeleteFileRequest;
use App\Http\Requests\Api\Group\EditGroupNameRequest;
use App\Http\Requests\Api\Group\RemoveMemberRequest;
use App\Http\Resources\Api\Group\GroupDetailsResource;
use App\Models\Group;
use App\Services\GroupService\GroupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery\Exception;

class GroupController extends BaseApiController
{
    public function __construct(
        protected GroupService $groupService,
    )
    {
    }

    public function create(CreateGroupRequest $request): JsonResponse
    {
        try {

            $this->groupService->createGroupWithMembers(CreateGroupDTO::fromArray($request->validated()));

            return $this->sendSuccess('created successfully', []);

        } catch (\Exception $e) {

            return $this->sendError($e->getMessage(), StatusCodeEnum::BAD_REQUEST);

        }

    }

    public function show(int $id): JsonResponse
    {
        try {

            $group = $this->groupService->show($id);

            return $this->sendSuccess("fetched successfully", GroupDetailsResource::make($group));

        } catch (\DomainException $domainException) {

            return $this->sendError($domainException->getMessage(), StatusCodeEnum::BAD_REQUEST);

        } catch (Exception $exception) {

            return $this->sendError($exception->getMessage(), StatusCodeEnum::INTERNAL_SERVER_ERROR);

        }
    }

    public function showWithfiles(int $id): JsonResponse
    {
        try {

            $group = $this->groupService->showWithfiles($id);

            return $this->sendSuccess("fetched successfully", $group);

        } catch (\DomainException $domainException) {

            return $this->sendError($domainException->getMessage(), StatusCodeEnum::BAD_REQUEST);

        } catch (Exception $exception) {

            return $this->sendError($exception->getMessage(), StatusCodeEnum::INTERNAL_SERVER_ERROR);

        }
    }

    public function exitGroup(int $group_id): JsonResponse
    {

        try {

            $this->groupService->exitGroup($group_id);

            return $this->sendSuccess("exited successfully");

        } catch (\DomainException $domainException) {

            return $this->sendError($domainException->getMessage(), StatusCodeEnum::BAD_REQUEST);

        } catch (\Exception $exception) {

            return $this->sendError($exception->getMessage(), StatusCodeEnum::INTERNAL_SERVER_ERROR);

        }

    }

    public function editGroupName(EditGroupNameRequest $request): JsonResponse
    {
        try {
            $edit_group_data = EditGroupNameDTO::fromArray($request->validated());

            $group_id = $edit_group_data->group_id;
            $new_name = $edit_group_data->name;

            $groupDTO = $this->groupService->edit_group_name($group_id, $new_name);

            return $this->sendSuccess("group's name changed", $groupDTO);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), StatusCodeEnum::BAD_REQUEST);
        }
    }

    public function removeMember(RemoveMemberRequest $request): JsonResponse
    {
        try {
            $DTO = RemoveMembersDTO::fromArray($request->validated());
            $response = $this->groupService->removeMember($DTO->group_id, $DTO->members);
            return $this->sendSuccess($response, []);
        } catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), StatusCodeEnum::BAD_REQUEST);
        }
    }

    public function getUserReport(Request $request): JsonResponse
    {
        try {
            $data = $this->groupService->getUserReport($request->group_id);
            return $this->sendSuccess("fetched successfully", $data);
        } catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), StatusCodeEnum::BAD_REQUEST);
        }
    }

}
