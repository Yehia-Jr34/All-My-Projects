<?php

namespace App\Http\Requests\Api\Ratings;

use App\Http\Requests\BaseRequest;

class AddRatingRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'training_id' => 'required|exists:trainings,id',
            'value' => 'required|numeric|min:1|max:5',
        ];
    }
}
