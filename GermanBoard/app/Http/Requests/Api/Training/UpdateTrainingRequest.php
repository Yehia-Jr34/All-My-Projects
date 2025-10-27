<?php

namespace App\Http\Requests\Api\Training;

use App\Http\Requests\BaseRequest;

class UpdateTrainingRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:trainings,id',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'title_du' => 'string|max:255',
            'about_ar' => 'required|string',
            'about_en' => 'required|string',
            'about_du' => 'string',
            'start_date' => [
                'required_if:type,live,onsite',
                'nullable',
                'date',
            ],
            'end_date' => [
                'required_if:type,live,onsite',
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
            'duration_in_hours' => 'required|integer',
            'price' => 'required|numeric|min:0',
            'type' => 'required|string|in:recorded,live,onsite',
            'language' => 'required|string|in:en,ar,du',
            'key_learning_objectives' => 'required|array',
            'key_learning_objectives.*' => 'required|string',
            'tags' => 'required|array',
            'tags.*' => 'required|string',
            'category_id' => 'required|integer|exists:categories,id',
            'training_site' => 'string',
        ];
    }
}
