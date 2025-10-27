<?php

namespace App\Http\Requests\Api\Membership;

use App\Http\Requests\BaseRequest;

class UpdateMembershipRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'membership_start_date' => 'date',
            'membership_expiry_date' => 'date|after:membership_start_date',
        ];
    }
}
