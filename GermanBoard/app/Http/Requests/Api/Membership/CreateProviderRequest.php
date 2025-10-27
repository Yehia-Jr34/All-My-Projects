<?php

namespace App\Http\Requests\Api\Membership;

use App\Http\Requests\BaseRequest;

class CreateProviderRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'membership_start_date' => 'required|date',
            'membership_expiry_date' => 'required|date',
            'role' => 'required|string|min:3',
        ];
    }
}
