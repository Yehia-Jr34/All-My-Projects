<?php

namespace App\Http\Requests\Api\Membership;

use App\Http\Requests\BaseRequest;

class ReactiveMembershipRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider_id' => 'required|integer|exists:providers,id',
            'membership_start_date' => 'required|date',
            'membership_expiry_date' => 'required|date',
        ];
    }

}
