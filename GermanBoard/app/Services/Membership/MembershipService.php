<?php

namespace App\Services\Membership;

use App\Enum\NotificationTypesEnum;
use App\Enum\StatusCodeEnum;
use App\Models\MemberShip;
use App\Models\Notification;
use App\Repositories\Contracts\MembershipRepositoryInterface;
use Carbon\Carbon;

class MembershipService
{
    public function __construct(
        private MembershipRepositoryInterface $membershipRepository,
    ) {}

    public function add(array $data): array
    {
        $new_membership_data = [
            'provider_id' => $data['provider_id'],
            'start_at' => $data['membership_start_date'],
            'expired_at' => $data['membership_expiry_date'],
            'remind_at' => Carbon::parse($data['membership_expiry_date'])->subMonth()->format('Y-m-d'),
        ];

        $membership = $this->membershipRepository->create($new_membership_data);
        return $membership->toArray();
    }

    public function invoke($membership_id): void
    {
        $is_updated = $this->membershipRepository->update($membership_id, ['is_revoked' => true]);

        if (!$is_updated) {
            throw new \DomainException('Membership not found', StatusCodeEnum::NOT_FOUND->value);
        }

        Notification::create([
            'title' => "Membership Revoked",
            'body' => "Admins Have Been Revoked Your Membership, You won't be able to store data on the system",
            'user_id' => MemberShip::find($membership_id)->provider->user->id,
            'notification_type' => NotificationTypesEnum::MEMBERSHIP_REVOKED->value
        ]);
    }

    public function destroy($membership_id): void
    {
        $is_deleted = $this->membershipRepository->destroy($membership_id);

        if (!$is_deleted) {
            throw new \DomainException('Membership not found', StatusCodeEnum::NOT_FOUND->value);
        }
    }

    public function update($membership_id, array $data)
    {
        $is_updated = $this->membershipRepository->update($membership_id, [
            'start_at' => $data['membership_start_date'],
            'expired_at' => $data['membership_expiry_date'],
            'remind_at' => Carbon::parse($data['membership_expiry_date'])->subMonth()->format('Y-m-d'),
        ]);
        if (!$is_updated) {
            throw new \DomainException('Membership not found', StatusCodeEnum::NOT_FOUND->value);
        }
    }
}
