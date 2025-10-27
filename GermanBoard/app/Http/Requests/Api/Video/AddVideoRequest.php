<?php

namespace App\Http\Requests\Api\Video;

use App\Http\Requests\BaseRequest;

class AddVideoRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'video' => 'required|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime',
            'position' => 'required|integer|min:1',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'training_id' => 'required|integer|exists:trainings,id',
        ];
    }
}
