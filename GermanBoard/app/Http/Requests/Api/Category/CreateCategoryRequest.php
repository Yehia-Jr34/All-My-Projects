<?php

namespace App\Http\Requests\Api\Category;

use App\Http\Requests\BaseRequest;

class CreateCategoryRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name_en' => 'required|string',
            'name_ar' => 'required|string',
            'name_du' => 'required|string',
        ];
    }
}
