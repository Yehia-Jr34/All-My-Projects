<?php

namespace App\Http\Requests\Api\Category;

use App\Http\Requests\BaseRequest;

class UpdateCategoryRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name_en' => 'required|string',
            'name_ar' => 'required|string',
            'name_du' => 'required|string',
            'category_id' => 'required|exists:categories,id'
        ];
    }
}
