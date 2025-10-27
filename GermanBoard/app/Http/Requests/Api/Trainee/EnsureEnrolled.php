<?php

namespace App\Http\Requests\Api\Trainee;

use App\Http\Requests\BaseRequest;

class EnsureEnrolled extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'training_id' => 'required|integer|exists:trainings,id',
        ];
    }
}
