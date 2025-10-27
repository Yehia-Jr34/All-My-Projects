<?php

namespace App\Http\Requests\Api\Blog;

use App\Http\Requests\BaseRequest;

class CreateBlogRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'image' => 'required|image',
            'category_id' => 'required|integer|exists:categories,id',
            'content' => 'required|string',
        ];
    }

}
