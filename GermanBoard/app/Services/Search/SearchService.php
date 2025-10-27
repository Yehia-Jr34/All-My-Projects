<?php

namespace App\Services\Search;

use App\Repositories\Contracts\GlobalArticleRepositoryInterface;
use App\Repositories\Contracts\ProviderRepositoryInterface;
use App\Repositories\Contracts\TrainingRepositoryInterface;

class SearchService
{
    public function __construct(
        private readonly ProviderRepositoryInterface      $providerRepository,
        private readonly GlobalArticleRepositoryInterface $globalArticleRepository,
        private readonly TrainingRepositoryInterface      $trainingRepository,
    )
    {
    }

    public function search(string|null $query): array
    {
        if (!$query){
            return [
                'providers' => [],
                'articles' => [],
                'trainings' => [],
            ];
        }

        $providers = $this->providerRepository->search($query);
        $articles = $this->globalArticleRepository->search($query);
        $trainings = $this->trainingRepository->search($query);

        return [
            'providers' => $providers,
            'articles' => $articles,
            'trainings' => $trainings,
        ];

    }
}
