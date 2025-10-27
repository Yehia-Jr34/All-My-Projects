<?php

namespace App\Http\Requests\Api\TrainingSession;

use App\Enum\SessionStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class CreateSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titles' => 'required|string',
            'start_date' => 'required|string',
            'status' => 'required|enum:'.SessionStatusEnum::class,
            'training_session_id' => 'required|integer|exists:training_sessions,id',
        ];
    }
}
