<?php

namespace App\Http\Requests\Api\Group;

use Illuminate\Foundation\Http\FormRequest;

class RemoveMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group_id' => 'required|numeric',
            'members' => 'array|nullable',
            'members.*' => "numeric|exists:users,id",
        ];
    }
}
