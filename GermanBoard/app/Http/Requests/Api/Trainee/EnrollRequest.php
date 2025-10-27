<?php

namespace App\Http\Requests\Api\Trainee;

use Illuminate\Foundation\Http\FormRequest;

class EnrollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'training_id' => 'required|integer|exists:trainings,id',
            'amount' => 'required|string',
            'currency' => 'required|string',
        ];
    }
}
