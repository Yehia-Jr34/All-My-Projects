<?php

namespace App\Http\Requests\Api\PaymentRequest;

use App\Http\Requests\BaseRequest;

class CreatePaymentRequestRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric',
            'account_number' => 'required|string'
        ];
    }
}
