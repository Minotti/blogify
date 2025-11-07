<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $post->title }} - Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --color-jsonplaceholder: #007bff;
            --color-fakestore: #fd7e14;
        }
        .article-header {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 2rem;
            margin-bottom: 2rem;
        }
        .article-title {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1rem;
        }
        .article-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
            color: #6c757d;
        }
        .article-content {
            font-size: 1.125rem;
            line-height: 1.8;
            color: #333;
            margin-bottom: 3rem;
        }
        .article-content p {
            margin-bottom: 1.5rem;
        }
        .source-badge-jsonplaceholder {
            background-color: var(--color-jsonplaceholder);
            color: white;
        }
        .source-badge-fakestore {
            background-color: var(--color-fakestore);
            color: white;
        }
        .breadcrumb-nav {
            background: transparent;
            padding: 0;
            margin-bottom: 2rem;
        }
        .article-footer {
            border-top: 2px solid #e9ecef;
            padding-top: 2rem;
            margin-top: 3rem;
        }
    </style>
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                <i class="bi bi-journal-text"></i> Blogify
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('public.posts.index') }}">All Posts</a>
                <a class="nav-link" href="{{ route('home') }}">Home</a>
                @auth
                    <a class="nav-link" href="{{ route('posts.index') }}">My Posts</a>
                @else
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                @endauth
            </div>
        </div>
    </nav>
</header>

<main class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb" class="breadcrumb-nav">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('public.posts.index') }}">Blog</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($post->title, 30) }}</li>
                    </ol>
                </nav>

                <article>
                    <header class="article-header">
                        <h1 class="article-title">{{ $post->title }}</h1>
                        <div class="article-meta">
                            <span>
                                <i class="bi bi-calendar3"></i>
                                Published {{ $post->created_at->format('F j, Y') }}
                            </span>
                            @if($post->source)
                                <span class="badge source-badge-{{ $post->source }}">
                                    <i class="bi bi-tag"></i> {{ ucfirst($post->source) }}
                                </span>
                            @endif
                            @if($post->author)
                                <span>
                                    <i class="bi bi-person"></i>
                                    {{ $post->author->name }}
                                </span>
                            @endif
                        </div>
                    </header>

                    <div class="article-content">
                        {!! nl2br(e($post->content)) !!}
                    </div>

                    <footer class="article-footer">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <a href="{{ route('public.posts.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Posts
                            </a>
                            <div class="text-muted">
                                <small>
                                    <i class="bi bi-clock"></i>
                                    {{ $post->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    </footer>
                </article>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>
</html>

