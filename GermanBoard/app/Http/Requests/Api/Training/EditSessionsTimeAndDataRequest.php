<?php

namespace App\Http\Requests\Api\Training;

use App\Rules\DateTimeRule;
use Illuminate\Foundation\Http\FormRequest;

class EditSessionsTimeAndDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'session_id' => 'required|integer|exists:training_sessions,id',
            'start_date' => ['required', new DateTimeRule],
            'notes' => 'string|nullable',
            'title' => 'string|nullable',
        ];
    }
}
