<?php

namespace App\Services\Transformers;

use App\Contracts\Services\PostTransformerInterface;
use App\DTOs\PostImportDTO;
use App\Enums\ImportSource;

class JsonPlaceholderTransformer implements PostTransformerInterface
{
    /**
     * Transform JSONPlaceholder post data into standardized format
     *
     * @param array<string, mixed> $data JSONPlaceholder post data
     * @return PostImportDTO Transformed post data as DTO
     */
    public function transform(array $data): PostImportDTO
    {
        return PostImportDTO::fromArray([
            'title' => $data['title'] ?? '',
            'content' => $data['body'] ?? '',
            'source' => ImportSource::JSON_PLACEHOLDER->value,
            'external_id' => (string) ($data['id'] ?? ''),
        ]);
    }
}

