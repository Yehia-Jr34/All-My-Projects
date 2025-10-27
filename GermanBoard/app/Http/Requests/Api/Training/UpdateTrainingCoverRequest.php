<?php

namespace App\Http\Requests\Api\Training;

use App\Http\Requests\BaseRequest;

class UpdateTrainingCoverRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:trainings,id',
            'cover' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5000',
        ];
    }
}
