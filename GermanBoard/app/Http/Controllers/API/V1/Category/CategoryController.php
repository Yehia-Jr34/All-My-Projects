<?php

namespace App\Http\Controllers\API\V1\Category;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\Api\Category\CreateCategoryRequest;
use App\Http\Requests\Api\Category\UpdateCategoryRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Services\Category\CategoryService;
use Illuminate\Http\JsonResponse;


class CategoryController extends BaseApiController
{

    public function __construct(
        private readonly CategoryService $categoryService
    ) {}

    public function get(): JsonResponse
    {
        $response = $this->categoryService->get();

        return $this->sendSuccess("fetched successfully", CategoryResource::collection($response), StatusCodeEnum::OK->value);
    }

    public function getAdmin(): JsonResponse
    {
        $response = $this->categoryService->get();

        return $this->sendSuccess("fetched successfully", ($response), StatusCodeEnum::OK->value);
    }

    public function create(CreateCategoryRequest $request){

        $this->categoryService->create($request->validated());

        return $this->sendSuccess("created successfully",  StatusCodeEnum::OK->value);

    }

    public function update(UpdateCategoryRequest $request){

        $this->categoryService->update($request->validated());

        return $this->sendSuccess("updated successfully",  StatusCodeEnum::OK->value);

    }
}
