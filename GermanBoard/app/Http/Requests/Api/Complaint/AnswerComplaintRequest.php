<?php

namespace App\Http\Requests\Api\Complaint;

use App\Http\Requests\BaseRequest;

class AnswerComplaintRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'complaint_id' => 'required|integer|exists:complaints,id',
            'complaint_status' => 'required|in:closed,resolved,rejected',
            'answer' => 'required|string'
        ];
    }
}
