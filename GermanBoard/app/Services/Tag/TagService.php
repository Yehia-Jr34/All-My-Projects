<?php

declare(strict_types=1);

namespace App\Services\Tag;

use App\Repositories\Contracts\TagRepositoryInterface;
use Illuminate\Support\Collection;

class TagService
{
    public function __construct(
        private readonly TagRepositoryInterface $tagRepository
    ){}

    public function get(string $searchQuery): Collection{

        return $this->tagRepository->get($searchQuery);

    }
}
