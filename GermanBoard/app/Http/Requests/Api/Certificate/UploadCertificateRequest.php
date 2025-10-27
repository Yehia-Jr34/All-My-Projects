<?php

namespace App\Http\Requests\Api\Certificate;

use App\Http\Requests\BaseRequest;

class UploadCertificateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'training_trainee_id' => 'integer|nullable',
            'certificate' => 'required|file|mimes:jpeg,png,jpg,gif,svg',
            'code' => 'required|string'
        ];
    }
}
