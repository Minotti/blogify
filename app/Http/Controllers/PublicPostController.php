<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Repositories\PostRepositoryInterface;

class PublicPostController extends Controller
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
    ) {
    }

    /**
     * Display a listing of published posts
     */
    public function index()
    {
        $posts = $this->postRepository->getPublished(12);

        return view('public.index', compact('posts'));
    }

    /**
     * Display the specified published post
     */
    public function show(Post $post)
    {
        // Only show published posts
        if (!$post->status->isPublished()) {
            abort(404);
        }

        return view('public.show', compact('post'));
    }
}
