<?php

namespace App\Http\Requests\Api\TrainingSession;

use Illuminate\Foundation\Http\FormRequest;

class TrainingSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'channel_name' => 'required|string',
            'training_id' => 'required|integer|exists:trainings,id',
            'training_session_id' => 'required|integer|exists:training_sessions,id',
        ];
    }
}
