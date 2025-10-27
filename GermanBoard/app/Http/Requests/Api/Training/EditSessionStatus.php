<?php

namespace App\Http\Requests\Api\Training;

use App\Enum\SessionStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class EditSessionStatus extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'session_id' => 'required|integer|exists:training_sessions,id',
            'status' => 'in:' . implode(',', array_column(SessionStatusEnum::cases(), 'value')),
        ];
    }
}
