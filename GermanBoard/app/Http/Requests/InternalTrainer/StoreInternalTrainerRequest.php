<?php

namespace App\Http\Requests\InternalTrainer;

use App\Http\Requests\BaseRequest;

class StoreInternalTrainerRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true; // Allow request, adjust if needed
    }

    public function rules(): array
    {
        return [
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'phone_number'   => 'required|string|max:20',
            'password' => 'required|string|min:8',
            'profile_image'  => 'nullable|image|max:5120',
            'gender' => 'required|in:male,female'
        ];
    }
}
