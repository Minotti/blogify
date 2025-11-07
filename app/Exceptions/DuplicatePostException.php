<?php

namespace App\Exceptions;

use App\Models\Post;

class DuplicatePostException extends ImportException
{
    public function __construct(
        string $message,
        public readonly Post $existingPost,
    ) {
        parent::__construct($message);
    }

    /**
     * Create exception for duplicate post
     */
    public static function create(Post $post): self
    {
        return new self(
            "This post has already been imported (ID: {$post->id})",
            $post,
        );
    }
}

