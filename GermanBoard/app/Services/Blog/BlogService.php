<?php

namespace App\Services\Blog;

use App\Enum\ArticlesStatusEnum;
use App\Enum\BlogMediaCollectionEnum;
use App\Enum\RolesEnum;
use App\Enum\StatusCodeEnum;
use App\Models\GlobalArticle;
use App\Repositories\Contracts\GlobalArticleCategoryRepositoryInterface;
use App\Repositories\Contracts\GlobalArticleRepositoryInterface;
use Illuminate\Support\Facades\DB;

class BlogService
{
    public function __construct(
        private readonly GlobalArticleRepositoryInterface         $articleRepository,
        private readonly GlobalArticleCategoryRepositoryInterface $globalArticleCategoryRepository
    )
    {
    }

    public function getAll()
    {
        return $this->articleRepository->getAll();
    }

    public function getById(int $id)
    {
        return $this->articleRepository->getById($id);
    }

    public function addView(int $id): void
    {
        $this->articleRepository->addView($id);
    }

    public function addArticle(array $data): array
    {
       return DB::transaction(function () use ($data){
            $user = request()->user();

            $article_data = [
                'title' => $data['title'],
                'content' => $data['content'],
                'provider_id' => $user->provider->id,
            ];

            if($user->hasRole(RolesEnum::ADMIN->value)){
                $article_data['status']= ArticlesStatusEnum::APPROVED->value;
            }

            $article = $this->articleRepository->create($article_data);

            $article->addMedia($data['image'])
                ->toMediaCollection(BlogMediaCollectionEnum::Image->value);


            $this->globalArticleCategoryRepository->add([
                'global_article_id' => $article->id,
                'category_id' => $data['category_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $article->toArray();
        });

    }

    public function editArticle(array $data): array
    {
        return DB::transaction(function() use($data) {
            $user = request()->user();

            $article = $this->articleRepository->getById($data['article_id']);

            if ($article->provider_id !== $user->provider->id) {
                throw new \Exception("You can not edit this article", [], StatusCodeEnum::UNAUTHORIZED->value);
            }

            $status = ArticlesStatusEnum::PENDING->value;

            if($user->hasRole(RolesEnum::ADMIN->value)){
                $status = ArticlesStatusEnum::APPROVED->value;
            }

            $article_data = [
                'title' => $data['title'],
                'content' => $data['content'],
                'status' => $status
            ];

            // Handle cover image update if new image is provided
            if (isset($data['image'])) {
                // Delete old cover image if exists
                $article->clearMediaCollection(BlogMediaCollectionEnum::Image->value);

                // Add new cover image
                $article->addMedia($data['image'])
                    ->toMediaCollection(BlogMediaCollectionEnum::Image->value);
            }

            $article->global_article_categories->first()->update([
                'category_id' => $data['category_id']
            ]);

            $article = $this->articleRepository->update($article->id, $article_data);



            return $article->toArray();
        } );

    }
    public function deleteArticle(int $id): void
    {
        $user = request()->user();

        $article = $this->articleRepository->getById($id);
        if ($article->provider_id !== $user->provider->id) {
            throw new \Exception("You can not delete this article", [], StatusCodeEnum::UNAUTHORIZED);
        }

        $this->articleRepository->delete($article->id);

    }

    public function adminAll()
    {
        $status = request('status');

        return $this->articleRepository->getAllWithProvider($status);
    }

    public function reject($data)
    {
        return $this->articleRepository->update($data['article_id'],[
            'reject_reason' => $data['reject_reason'],
            'status' => ArticlesStatusEnum::REJECTED->value,
        ]);
    }

    public function accept($id)
    {
        return $this->articleRepository->update($id,[
            'status' => ArticlesStatusEnum::APPROVED->value,
            'reject_reason' => null,
        ]);
    }

    public function getMyArticles()
    {
        $provider = auth()->user()->provider;

        $status = request('status');
        return  $this->articleRepository->getArticlesByProvider($provider->id,$status);
    }

    public function show(int $id)
    {
        $user = auth()->user();
        $article = $this->articleRepository->getById($id);

        if($user->hasRole(RolesEnum::ADMIN->value)){
            return $article;
        }

        $ownedByProvider = $article->provider_id == $user->provider->id;

        if(!$ownedByProvider){
            throw new \DomainException("unauthorized", StatusCodeEnum::UNAUTHORIZED->value);
        }

        return $article;
    }

    public function byProvider($id)
    {
        $articles =  GlobalArticle::select(
            ['id','provider_id','title' , 'views','status','created_at']
        )->where('provider_id' ,$id)->get();

        return $articles->map(function ($art){
            return array_merge($art->toArray() , [
                'image' => $art->getFirstMediaUrl(BlogMediaCollectionEnum::Image->value)
            ]);
        });
    }
}
