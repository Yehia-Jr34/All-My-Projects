<?php

namespace App\Http\Controllers\API\V1\Membership;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Membership\ReactiveMembershipRequest;
use App\Http\Requests\Api\Membership\UpdateMembershipRequest;
use App\Services\Membership\MembershipService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MembershipController extends BaseApiController
{
    public function __construct(
        private readonly MembershipService $membershipService
    ) {}

    public function create(ReactiveMembershipRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->membershipService->add($data);
        return $this->sendSuccess('Membership successfully reactivated', $response, StatusCodeEnum::CREATED->value);
    }

    public function invoke($membership_id): JsonResponse
    {
        $this->membershipService->invoke($membership_id);

        return $this->sendSuccess('Membership successfully updated', null, StatusCodeEnum::OK->value);
    }

    public function destroy($membership_id): JsonResponse
    {
        $this->membershipService->destroy($membership_id);

        return $this->sendSuccess('Membership successfully deleted', null, StatusCodeEnum::OK->value);
    }

    public function update(UpdateMembershipRequest $request, $membership_id): JsonResponse
    {
        $data = $request->validated();
        $response = $this->membershipService->update($membership_id, $data);
        return $this->sendSuccess('Membership successfully updated', $response, StatusCodeEnum::OK->value);
    }
}
