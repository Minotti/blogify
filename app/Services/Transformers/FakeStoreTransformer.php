<?php

namespace App\Services\Transformers;

use App\Contracts\Services\PostTransformerInterface;
use App\DTOs\PostImportDTO;
use App\Enums\ImportSource;

class FakeStoreTransformer implements PostTransformerInterface
{
    /**
     * Transform FakeStore product data into blog post format
     *
     * @param array<string, mixed> $data FakeStore product data
     * @return PostImportDTO Transformed post data as DTO
     */
    public function transform(array $data): PostImportDTO
    {
        $description = $data['description'] ?? '';
        $price = $data['price'] ?? null;
        $category = $data['category'] ?? '';

        // Enhance content with product information
        $content = $description;

        if ($price !== null) {
            $content .= "\n\nPrice: $" . number_format($price, 2);
        }

        if ($category) {
            $content .= "\n\nCategory: " . ucfirst($category);
        }

        return PostImportDTO::fromArray([
            'title' => $data['title'] ?? '',
            'content' => trim($content),
            'source' => ImportSource::FAKE_STORE->value,
            'external_id' => (string) ($data['id'] ?? ''),
        ]);
    }
}

