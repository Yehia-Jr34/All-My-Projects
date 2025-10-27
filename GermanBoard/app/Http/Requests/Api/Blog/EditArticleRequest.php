<?php

namespace App\Http\Requests\Api\Blog;

use App\Http\Requests\BaseRequest;

class EditArticleRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'string',
            'content' => 'string',
            'image'=>'image',
            'article_id' => 'required|integer|exists:global_articles,id',
            'category_id' => 'required|integer|exists:categories,id',
        ];
    }

}
