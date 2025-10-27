<?php

namespace App\Http\Requests\Api\Training;

use App\Http\Requests\BaseRequest;

class GetTrainingVideosRequest extends BaseRequest
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
