<?php

namespace App\Http\Controllers\API\V1\Blog;

use App\Enum\StatusCodeEnum;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\Api\Blog\CreateBlogRequest;
use App\Http\Requests\Api\Blog\EditArticleRequest;
use App\Http\Requests\Api\Blog\RejectArticleRequest;
use App\Http\Resources\Blog\GetAllBlogsResource;
use App\Http\Resources\Blog\GetBlogDetailsResource;
use App\Http\Resources\Blog\GetAllForAdminArticleResource;
use App\Http\Resources\Blog\GetMyArticleResource;
use App\Services\Blog\BlogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogController extends BaseApiController
{
    public function __construct(
        private BlogService $blogService
    )
    {
    }

    public function getAll(): JsonResponse
    {
//        dd($this->blogService->getAll());
        $data = $this->blogService->getAll();
        $response = [
            'data' => GetAllBlogsResource::collection($data->all()),
            'total' => $data->total(),
            'perPage' => $data->perPage(),
            'currentPage' => $data->currentPage(),
        ];

        return $this->sendSuccess('blogs fetched successfully', $response , StatusCodeEnum::OK->value);
    }

    public function getById(int $id): JsonResponse
    {
        $response = $this->blogService->getById($id);
        return $this->sendSuccess('blog fetched successfully', GetBlogDetailsResource::make($response), StatusCodeEnum::OK->value);
    }

    public function addView(Request $request): JsonResponse
    {
        $this->blogService->addView($request->global_article_id);
        return $this->sendSuccess('view added successfully', [], StatusCodeEnum::OK->value);
    }

    public function adminAll():JsonResponse{

        $data = $this->blogService->adminAll();

        $response = [
            'articles' => GetAllForAdminArticleResource::collection($data),
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'per_page' => $data->perPage(),
            'total' => $data->total(),
        ];

        return $this->sendSuccess('article deleted successfully', $response, StatusCodeEnum::OK->value);

    }

    public function accept(int $id): JsonResponse
    {
        $this->blogService->accept($id);
        return $this->sendSuccess('article accepted successfully', [], StatusCodeEnum::OK->value);

    }

    public function reject(RejectArticleRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->blogService->reject($data);
        return $this->sendSuccess('article rejected successfully', [], StatusCodeEnum::OK->value);

    }

    public function getMyArticle():JsonResponse{
        $data = $this->blogService->getMyArticles();

        $response = [
            'articles' => GetMyArticleResource::collection($data),
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'per_page' => $data->perPage(),
            'total' => $data->total(),
        ];

        return $this->sendSuccess('article deleted successfully', $response, StatusCodeEnum::OK->value);

    }

    public function addArticle(CreateBlogRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->blogService->addArticle($data);
        return $this->sendSuccess('article added successfully', $response, StatusCodeEnum::OK->value);
    }

    public function show($id): JsonResponse
    {
        $response = $this->blogService->show($id);
        return $this->sendSuccess('article added successfully', GetMyArticleResource::make($response), StatusCodeEnum::OK->value);
    }

    public function editArticle(EditArticleRequest $request): JsonResponse
    {
        $data = $request->validated();
        $response = $this->blogService->editArticle($data);
        return $this->sendSuccess('article edited successfully', $response, StatusCodeEnum::OK->value);
    }


    public function deleteArticle(int $id): JsonResponse
    {
        $this->blogService->deleteArticle($id);
        return $this->sendSuccess('article deleted successfully', [], StatusCodeEnum::OK->value);

    }

    public function byProvider($id){
        $data = $this->blogService->byProvider($id);

        return $this->sendSuccess('article deleted successfully', $data, StatusCodeEnum::OK->value);

    }

}
