<?php

namespace App\Http\Requests\Api\provider;

use App\Http\Requests\BaseRequest;

class CompleteProviderInfoRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|min:3',
            'last_name' => 'required|string|min:3',
            'phone_number' => 'required|string|min:3',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string|min:3',
            'brief' => 'required|string|min:3',
            'specialized_at' => 'required|string|min:3',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'cover' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'password' => 'required|confirmed|string|min:8',
            'location'=>'string',
            'work_phone_number'=>'string',
            'nickname'=>'string',
        ];
    }
}
