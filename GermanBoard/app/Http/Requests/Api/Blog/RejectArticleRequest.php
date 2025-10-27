<?php

namespace App\Http\Requests\Api\Blog;

use App\Http\Requests\BaseRequest;

class RejectArticleRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'article_id' => 'required|exists:global_articles,id',
            'reject_reason'=>'string|nullable'
        ];
    }

}
