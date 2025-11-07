<?php

namespace App\DTOs;

use App\Enums\ImportSource;

readonly class PostImportDTO
{
    public function __construct(
        public string $title,
        public string $content,
        public ImportSource $source,
        public string $externalId,
    ) {
        $this->validate();
    }

    /**
     * Create from array
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? '',
            content: $data['content'] ?? '',
            source: ImportSource::from($data['source'] ?? ''),
            externalId: (string) ($data['external_id'] ?? ''),
        );
    }

    /**
     * Convert to array
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
            'source' => $this->source->value,
            'external_id' => $this->externalId,
        ];
    }

    /**
     * Validate DTO data
     *
     * @throws \InvalidArgumentException
     */
    private function validate(): void
    {
        if (empty($this->title)) {
            throw new \InvalidArgumentException('Title cannot be empty');
        }

        if (empty($this->content)) {
            throw new \InvalidArgumentException('Content cannot be empty');
        }

        if (empty($this->externalId)) {
            throw new \InvalidArgumentException('External ID cannot be empty');
        }
    }
}

