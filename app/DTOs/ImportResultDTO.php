<?php

namespace App\DTOs;

use App\Models\Post;

readonly class ImportResultDTO
{
    public function __construct(
        public bool $success,
        public string $message,
        public bool $duplicate = false,
        public ?Post $post = null,
    ) {
    }

    /**
     * Create success result
     */
    public static function success(Post $post, string $message = 'Post imported successfully as draft'): self
    {
        return new self(
            success: true,
            message: $message,
            duplicate: false,
            post: $post,
        );
    }

    /**
     * Create failure result
     */
    public static function failure(string $message): self
    {
        return new self(
            success: false,
            message: $message,
            duplicate: false,
            post: null,
        );
    }

    /**
     * Create duplicate result
     */
    public static function duplicate(Post $post, string $message = 'This post has already been imported'): self
    {
        return new self(
            success: false,
            message: $message,
            duplicate: true,
            post: $post,
        );
    }

    /**
     * Convert to array for JSON responses
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'duplicate' => $this->duplicate,
            'post' => $this->post?->toArray(),
        ];
    }
}

