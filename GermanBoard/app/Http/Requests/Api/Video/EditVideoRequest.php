<?php

namespace App\Http\Requests\Api\Video;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class EditVideoRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'video_id' => ['required', 'integer', 'exists:videos,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'position' => ['required', 'integer'],
        ];
    }

}
