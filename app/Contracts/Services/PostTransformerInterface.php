<?php

namespace App\Contracts\Services;

use App\DTOs\PostImportDTO;

interface PostTransformerInterface
{
    /**
     * Transform external API data into standardized post format
     *
     * @param array<string, mixed> $data Raw data from external API
     * @return PostImportDTO Transformed post data as DTO
     */
    public function transform(array $data): PostImportDTO;
}

