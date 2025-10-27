<?php

namespace App\Http\Requests\Api\Certificate;

use App\Http\Requests\BaseRequest;

class CertificateByCodeRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'code' => 'required|string',
        ];
    }
}
