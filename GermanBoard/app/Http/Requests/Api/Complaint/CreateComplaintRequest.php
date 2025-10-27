<?php

namespace App\Http\Requests\Api\Complaint;

use App\Http\Requests\BaseRequest;

class CreateComplaintRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'complaint' => 'required|string',
            'type' => 'required|in:technical,payment,content,other',
        ];
    }
}
