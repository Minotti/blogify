<?php

namespace App\Repositories;

use App\DTOs\PostImportDTO;
use App\Enums\ImportSource;
use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PostRepository implements PostRepositoryInterface
{
    public function __construct(
        private Post $model
    ) {
    }

    /**
     * Find post by source and external ID
     */
    public function findBySourceAndExternalId(ImportSource $source, string $externalId): ?Post
    {
        return $this->model
            ->where('source', $source->value)
            ->where('external_id', $externalId)
            ->first();
    }

    /**
     * Create a new post from import DTO
     */
    public function createFromImport(PostImportDTO $dto, int $userId, PostStatus $status = PostStatus::DRAFT): Post
    {
        return $this->model->create([
            'title' => $dto->title,
            'content' => $dto->content,
            'status' => $status->value,
            'source' => $dto->source->value,
            'external_id' => $dto->externalId,
            'user_id' => $userId,
        ]);
    }

    /**
     * Get published posts
     */
    public function getPublished(int $perPage = 12): LengthAwarePaginator
    {
        return $this->model
            ->where('status', PostStatus::PUBLISHED->value)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get recent imports
     */
    public function getRecentImports(int $limit = 10): Collection
    {
        return $this->model
            ->whereNotNull('source')
            ->latest()
            ->limit($limit)
            ->get();
    }
}

