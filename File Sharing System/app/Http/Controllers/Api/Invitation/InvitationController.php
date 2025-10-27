<?php

namespace App\Http\Controllers\Api\Invitation;

use App\Enums\StatusCodeEnum;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Invitation\CreateInvitationRequest;
use App\Services\Invitation\InvitationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;


class InvitationController extends BaseApiController
{
    public function __construct(
        protected InvitationService $invitationService
    )
    {
    }

    public function create(CreateInvitationRequest $request): JsonResponse
    {
        try {

            $this->invitationService->createInvitation($request->validated());

            return $this->sendSuccess('send the invitations', []);

        } catch (\DomainException $e) {

            return $this->sendError($e->getMessage(), StatusCodeEnum::BAD_REQUEST);

        } catch (\Exception $e) {

            return $this->sendError($e->getMessage(), StatusCodeEnum::INTERNAL_SERVER_ERROR);

        }

    }

    public function accept(int $invitation_id): JsonResponse
    {
        try {

            $this->invitationService->accept($invitation_id);

            return $this->sendSuccess('invitation accepted', []);
        } catch (\DomainException $e) {

            return $this->sendError($e->getMessage(), StatusCodeEnum::BAD_REQUEST);

        } catch (Exception $exception) {

            return $this->sendError($exception->getMessage(), StatusCodeEnum::INTERNAL_SERVER_ERROR);

        }
    }

    public function reject(int $invitation_id): JsonResponse
    {
        try {

            $this->invitationService->reject($invitation_id);

            return $this->sendSuccess('invitation rejected', []);

        } catch (\DomainException $e) {

            return $this->sendError($e->getMessage(), StatusCodeEnum::BAD_REQUEST);

        } catch (Exception $exception) {

            return $this->sendError($exception->getMessage(), StatusCodeEnum::INTERNAL_SERVER_ERROR);

        }
    }

    public function getInvitations(): JsonResponse
    {
        try {
            $user_id = Auth::user()->id;
            $data = $this->invitationService->getInvitations($user_id);
            return $this->sendSuccess('', $data, StatusCodeEnum::OK);
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage(), StatusCodeEnum::BAD_REQUEST);
        }

    }

}
