<?php

namespace App\Repositories\Contracts;

use App\Models\MemberShip;

interface MembershipRepositoryInterface
{
    public function create(array $data): Membership;
    public function makeLastMembershipInactive(int $provider_id): bool;
    public function update(int $membership_id, array $data): bool;
    public function destroy(int $membership_id): bool | null;
}
