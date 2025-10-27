<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|confirmed',
            'phone_number' => 'required|string|unique:trainees,phone_number',
            'gender' => 'required|string',
            'date_of_birth' => 'required|string',
            'country' => 'required|string',
            'address' => 'required|string',
        ];
    }
}
