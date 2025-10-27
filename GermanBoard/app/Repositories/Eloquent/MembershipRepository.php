<?php

namespace App\Repositories\Eloquent;

use App\Models\MemberShip;
use App\Repositories\Contracts\MembershipRepositoryInterface;

class MembershipRepository implements MembershipRepositoryInterface
{

    public function create(array $data): Membership
    {
        return MemberShip::create($data);
    }

    public function makeLastMembershipInactive(int $provider_id): bool
    {
        return MemberShip::where('provider_id', $provider_id)
            ->orderByDesc('created_at')
            ->first()
            ->update([
                'is_revoked' => true,
            ]);
    }

    public function update(int $membership_id, array $data): bool
    {
        return MemberShip::where('id', $membership_id)->first()->update($data);
    }

    public function destroy(int $membership_id): bool |null
    {
        return MemberShip::where('id', $membership_id)?->first()?->delete();
    }
}
