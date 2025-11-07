@extends('layouts.master')

@section('content')
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('posts.index') }}">My Posts</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($post->title, 30) }}</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="flex-grow-1">
                                <h1 class="card-title mb-3">{{ $post->title }}</h1>
                                <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                                    <span class="badge bg-{{ $post->status->isPublished() ? 'success' : 'secondary' }}">
                                        @if($post->status->isPublished())
                                            <i class="bi bi-check-circle"></i>
                                        @else
                                            <i class="bi bi-pencil-square"></i>
                                        @endif
                                        {{ ucfirst($post->status->value) }}
                                    </span>
                                    @if($post->source)
                                        <span class="badge source-badge-{{ $post->source }}">
                                            <i class="bi bi-cloud-download"></i> {{ ucfirst($post->source) }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-pencil-square"></i> Manual
                                        </span>
                                    @endif
                                    @if($post->external_id)
                                        <span class="badge bg-info">
                                            <i class="bi bi-link-45deg"></i> External ID: {{ $post->external_id }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-muted mb-3">
                                    <small>
                                        <i class="bi bi-calendar3"></i> Created: {{ $post->created_at->format('F j, Y \a\t g:i A') }}
                                        @if($post->updated_at != $post->created_at)
                                            <span class="ms-3">
                                                <i class="bi bi-pencil"></i> Updated: {{ $post->updated_at->format('F j, Y \a\t g:i A') }}
                                            </span>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="border-top pt-4 mb-4">
                            <h5 class="mb-3">Content</h5>
                            <div class="article-content">
                                {!! nl2br(e($post->content)) !!}
                            </div>
                        </div>

                        <div class="border-top pt-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Back to Posts
                                </a>
                            </div>
                            <div class="btn-group">
                                <a href="{{ route('posts.edit', $post) }}" class="btn btn-primary">
                                    <i class="bi bi-pencil"></i> Edit Post
                                </a>
                                <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete this post?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

