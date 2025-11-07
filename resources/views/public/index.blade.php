<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blog - Published Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --color-jsonplaceholder: #007bff;
            --color-fakestore: #fd7e14;
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
            margin-bottom: 3rem;
        }
        .post-card {
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            height: 100%;
        }
        .post-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }
        .source-badge-jsonplaceholder {
            background-color: var(--color-jsonplaceholder);
            color: white;
        }
        .source-badge-fakestore {
            background-color: var(--color-fakestore);
            color: white;
        }
        .card-gradient-jsonplaceholder {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            height: 4px;
        }
        .card-gradient-fakestore {
            background: linear-gradient(135deg, #fd7e14 0%, #e55a00 100%);
            height: 4px;
        }
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }
        .empty-state i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 1rem;
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
                <a class="nav-link" href="{{ route('home') }}">Home</a>
                @auth
                    <a class="nav-link" href="{{ route('posts.index') }}">My Posts</a>
                    <a class="nav-link" href="{{ route('import.index') }}">Import</a>
                @else
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                @endauth
            </div>
        </div>
    </nav>
</header>

<div class="hero-section">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3">
            <i class="bi bi-journal-text"></i> Welcome to Our Blog
        </h1>
        <p class="lead">Discover amazing content from various sources</p>
    </div>
</div>

<main class="pb-5">
    <div class="container">
        @if($posts->isEmpty())
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h3 class="text-muted">No published posts available yet</h3>
                <p class="text-muted">Check back later for new content!</p>
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
                                <div class="mb-2">
                                    @if($post->source)
                                        <span class="badge source-badge-{{ $post->source }} mb-2">
                                            <i class="bi bi-tag"></i> {{ ucfirst($post->source) }}
                                        </span>
                                    @endif
                                </div>
                                <h5 class="card-title fw-bold">{{ $post->title }}</h5>
                                <p class="card-text text-muted flex-grow-1">{{ Str::limit(strip_tags($post->content), 120) }}</p>
                                <div class="mt-auto">
                                    <small class="text-muted d-block mb-2">
                                        <i class="bi bi-calendar3"></i> {{ $post->created_at->format('M d, Y') }}
                                    </small>
                                    <a href="{{ route('public.posts.show', $post) }}" class="btn btn-primary w-100">
                                        Read More <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>
</html>

