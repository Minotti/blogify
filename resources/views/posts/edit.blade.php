@extends('layouts.master')

@section('content')
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('posts.index') }}">My Posts</a></li>
                <li class="breadcrumb-item"><a href="{{ route('posts.show', $post) }}">{{ Str::limit($post->title, 20) }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-white">
                        <h2 class="mb-0">
                            <i class="bi bi-pencil"></i> Edit Post
                        </h2>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('posts.update', $post) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="title" class="form-label fw-bold">
                                    Title <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="title" id="title"
                                       class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title', $post->title) }}"
                                       placeholder="Enter post title"
                                       required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label fw-bold">
                                    Content <span class="text-danger">*</span>
                                </label>
                                <textarea name="content" id="content" rows="12"
                                          class="form-control @error('content') is-invalid @enderror"
                                          placeholder="Write your post content here..."
                                          required>{{ old('content', $post->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label fw-bold">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select name="status" id="status"
                                        class="form-select @error('status') is-invalid @enderror"
                                        required>
                                    <option value="draft" {{ old('status', $post->status->value) === 'draft' ? 'selected' : '' }}>
                                        Draft
                                    </option>
                                    <option value="published" {{ old('status', $post->status->value) === 'published' ? 'selected' : '' }}>
                                        Published
                                    </option>
                                </select>
                                <div class="form-text">
                                    <i class="bi bi-info-circle"></i> Draft posts are not visible to the public. Published posts appear on the blog.
                                </div>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="source" class="form-label fw-bold">
                                    Source
                                </label>
                                <input type="text" name="source" id="source"
                                       class="form-control @error('source') is-invalid @enderror"
                                       value="{{ old('source', $post->source) }}"
                                       placeholder="e.g., jsonplaceholder, fakestore">
                                <div class="form-text">
                                    <i class="bi bi-info-circle"></i> Optional: Specify the source if this post was imported from an external API.
                                </div>
                                @error('source')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="external_id" class="form-label fw-bold">
                                    External ID
                                </label>
                                <input type="text" name="external_id" id="external_id"
                                       class="form-control @error('external_id') is-invalid @enderror"
                                       value="{{ old('external_id', $post->external_id) }}"
                                       placeholder="e.g., 123">
                                <div class="form-text">
                                    <i class="bi bi-info-circle"></i> Optional: The external ID from the source API (used for duplicate prevention).
                                </div>
                                @error('external_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center border-top pt-4">
                                <a href="{{ route('posts.show', $post) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Update Post
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

