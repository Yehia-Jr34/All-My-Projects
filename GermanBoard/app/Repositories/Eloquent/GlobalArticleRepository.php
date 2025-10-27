<?php

namespace App\Repositories\Eloquent;

use App\Enum\AppConstants;
use App\Enum\ArticlesStatusEnum;
use App\Models\GlobalArticle;
use App\Repositories\Contracts\GlobalArticleRepositoryInterface;
use Illuminate\Support\Collection;

class GlobalArticleRepository implements GlobalArticleRepositoryInterface
{
    public function getAll()
    {
        return GlobalArticle::with(['global_article_categories.category'])
            ->where('status',ArticlesStatusEnum::APPROVED->value)
            ->orderBy('created_at')
            ->paginate(10);

    }

    public function getById(int $id): GlobalArticle
    {
        return GlobalArticle::with(['global_article_categories.category'])->find($id);
    }

    public function search(string $query): Collection|null
    {
        return GlobalArticle::with(['provider.user'])->where('status',ArticlesStatusEnum::APPROVED->value)
            ->where('title', 'like', "%{$query}%")
            ->get();
    }

    public function addView(int $id): void
    {
        GlobalArticle::where('id', $id)
            ->increment('views');
    }

    public function create(array $data): GlobalArticle
    {
        return GlobalArticle::create($data);
    }

    public function update(int $id, array $data): GlobalArticle
    {
        $article = GlobalArticle::find($id);
        if(!$article){
            throw new \DomainException('not found',404);
        }
        $article->update($data);

        return $article;
    }

    public function delete(int $id): void
    {
        $tmp = $this->getById($id);
        $tmp->delete();
    }

    public function getAllWithProvider($status)
    {
        if($status){
            return GlobalArticle::with('provider.user')
                ->whereDoesntHave('provider.user', function($query) {
                    $query->where('email', AppConstants::ADMIN_EMAIL->value);
                })
                ->where('status',$status)
                ->orderBy('id', 'DESC')
                ->paginate(5);
        }

        return GlobalArticle::with('provider.user')
            ->whereDoesntHave('provider.user', function($query) {
                $query->where('email', AppConstants::ADMIN_EMAIL->value);
            })
            ->orderBy('id', 'DESC')
            ->paginate(5);
    }

    public function getArticlesByProvider($provider_id,  $status)
    {
        if($status){
            return GlobalArticle::with('provider.user')
                ->where('status',$status)
                ->where('provider_id',$provider_id)
                ->orderBy('id', 'DESC')
                ->paginate(5);
        }

        return GlobalArticle::with('provider.user')
            ->orderBy('id', 'DESC')
            ->where('provider_id',$provider_id)
            ->paginate(5);
    }
}
