@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-1">
                    <i class="bi bi-journal-text"></i> My Posts
                </h1>
                <p class="text-muted mb-0">Manage your blog posts</p>
            </div>
            <a href="{{ route('posts.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create Post
            </a>
        </div>

        @if($posts->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 4rem; color: #dee2e6;"></i>
                <h3 class="text-muted mt-3">No posts found</h3>
                <p class="text-muted">Get started by creating your first post!</p>
                <a href="{{ route('posts.create') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-plus-circle"></i> Create Your First Post
                </a>
            </div>
        @else
            <div class="row g-4">
                @foreach($posts as $post)
                    <div class="col-md-6 col-lg-4">
                        <div class="card post-card h-100">
                            @if($post->source)
                                <div class="card-gradient-{{ $post->source }}"></div>
                            @endif
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="flex-grow-1">
                                        @if($post->source)
                                            <span class="badge source-badge-{{ $post->source }} mb-2">
                                                <i class="bi bi-cloud-download"></i> {{ ucfirst($post->source) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary mb-2">
                                                <i class="bi bi-pencil-square"></i> Manual
                                            </span>
                                        @endif
                                    </div>
                                    <span class="badge bg-{{ $post->status->isPublished() ? 'success' : 'secondary' }}">
                                        @if($post->status->isPublished())
                                            <i class="bi bi-check-circle"></i>
                                        @else
                                            <i class="bi bi-pencil-square"></i>
                                        @endif
                                        {{ ucfirst($post->status->value) }}
                                    </span>
                                </div>

                                <h5 class="card-title fw-bold mb-2">{{ $post->title }}</h5>
                                <p class="card-text text-muted flex-grow-1">{{ Str::limit(strip_tags($post->content), 100) }}</p>

                                <div class="mt-auto pt-3 border-top">
                                    <small class="text-muted d-block mb-2">
                                        <i class="bi bi-calendar3"></i> {{ $post->created_at->format('M d, Y') }}
                                        @if($post->updated_at != $post->created_at)
                                            <span class="ms-2">
                                                <i class="bi bi-pencil"></i> Updated {{ $post->updated_at->diffForHumans() }}
                                            </span>
                                        @endif
                                    </small>
                                    <div class="btn-group w-100" role="group">
                                        <a href="{{ route('posts.edit', $post) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <a href="{{ route('posts.show', $post) }}" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this post?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

