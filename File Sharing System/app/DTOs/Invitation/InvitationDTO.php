<?php

namespace App\DTOs\Invitation;

use App\DTOs\Group\GroupInfoDTO;

class InvitationDTO
{
    public function __construct(
        public readonly int $id,
        public readonly GroupInfoDTO $groupInfoDTO,
        public readonly string $status
    )
    {
    }

    public static function create(int $id , GroupInfoDTO $group, string $status): self
    {
        return new self (
            $id,
            groupInfoDTO: $group,
            status: $status
        );
    }
}
