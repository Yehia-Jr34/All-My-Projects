<?php

namespace App\Http\Requests\Api\Training;

use Illuminate\Foundation\Http\FormRequest;

class AddAttachmentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attachments' => 'required|array',
            'training_id' => 'required|integer|exists:trainings,id',
            'attachments.*.file' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx,zip|max:10240',
        ];
    }
}
