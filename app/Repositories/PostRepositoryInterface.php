<?php

namespace App\Repositories;

use App\DTOs\PostImportDTO;
use App\Enums\ImportSource;
use App\Enums\PostStatus;
use App\Models\Post;

interface PostRepositoryInterface
{
    /**
     * Find post by source and external ID
     */
    public function findBySourceAndExternalId(ImportSource $source, string $externalId): ?Post;

    /**
     * Create a new post from import DTO
     */
    public function createFromImport(PostImportDTO $dto, int $userId, PostStatus $status = PostStatus::DRAFT): Post;

    /**
     * Get published posts
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPublished(int $perPage = 12);

    /**
     * Get recent imports
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentImports(int $limit = 10);
}

