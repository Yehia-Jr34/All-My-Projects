<?php

namespace App\Http\Requests\Api\Training;

use App\Http\Requests\BaseRequest;

class AddSessionsRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'training_id' => 'required|integer|exists:trainings,id',
            'sessions' => 'required|array',
            'sessions.*.title' => 'required|string',
            'sessions.*.start_date' => 'required|string|date_format:Y-m-d H:i:s',
            'sessions.*.notes' => 'nullable|string',
        ];
    }
}
