<?php

namespace App\Http\Controllers\API\V1\Tag;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Tags\TagResource;
use App\Services\Category\CategoryService;
use App\Services\Tag\TagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class TagController extends BaseApiController
{

    public function __construct(
        private readonly TagService $categoryService
    ) {}

    public function get(): JsonResponse
    {
        $searchQuery = (string)(\request()->query('search'));

        $response = $this->categoryService->get($searchQuery);

        return $this->sendSuccess("fetched successfully", TagResource::collection($response), StatusCodeEnum::OK->value);
    }
}
