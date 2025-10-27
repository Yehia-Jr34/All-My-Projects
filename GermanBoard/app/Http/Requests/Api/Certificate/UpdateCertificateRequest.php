<?php

namespace App\Http\Requests\Api\Certificate;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateCertificateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'training_trainee_id' => 'required|integer|exists:training_trainees,id',
            'certificate' => 'file|mimes:jpeg,png,jpg,gif,svg',
            'certification_code' => [
                'string',
                'nullable',
                Rule::unique('training_trainees')->ignore($this->training_trainee_id, 'id')
            ]
        ];
    }
}
