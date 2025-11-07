@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <h1 class="mb-4">Import Posts from External APIs</h1>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">JSONPlaceholder</h5>
                                <p class="card-text text-muted">Import a random blog post from JSONPlaceholder API</p>
                                <form action="{{ route('import.store', 'jsonplaceholder') }}" method="POST" class="import-form" data-source="jsonplaceholder">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        <span class="btn-text">Import Post</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">FakeStore</h5>
                                <p class="card-text text-muted">Import a random product from FakeStore API (transformed to blog post)</p>
                                <form action="{{ route('import.store', 'fakestore') }}" method="POST" class="import-form" data-source="fakestore">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        <span class="btn-text">Import Product</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                @if($recentImports->isNotEmpty())
                    <h3 class="mb-3">Recent Imports</h3>
                    <div class="list-group">
                        @foreach($recentImports as $post)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="mb-1">{{ $post->title }}</h5>
                                        <p class="mb-1 text-muted">{{ Str::limit($post->content, 100) }}</p>
                                        <small class="text-muted">
                                            Source: <span class="badge bg-secondary">{{ $post->source }}</span>
                                            | Status: <span class="badge bg-{{ $post->status->isPublished() ? 'success' : 'secondary' }}">{{ ucfirst($post->status->value) }}</span>
                                            | Imported: {{ $post->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <a href="{{ route('posts.edit', $post) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.import-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;
                const button = form.querySelector('button[type="submit"]');
                const spinner = button.querySelector('.spinner-border');
                const btnText = button.querySelector('.btn-text');
                const originalText = btnText.textContent;

                // Show loading state
                spinner.classList.remove('d-none');
                btnText.textContent = 'Importing...';
                button.disabled = true;

                // Submit form via fetch for better UX
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || form.querySelector('input[name="_token"]').value
                    },
                    body: new URLSearchParams(new FormData(form))
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                .then(data => {
                    // Reset button
                    spinner.classList.add('d-none');
                    btnText.textContent = originalText;
                    button.disabled = false;

                    // Show message
                    if (data.success) {
                        showAlert('success', data.message);
                        // Reload page after 1 second to show new import
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        const alertType = data.duplicate ? 'warning' : 'danger';
                        showAlert(alertType, data.message);
                    }
                })
                .catch(error => {
                    // Reset button
                    spinner.classList.add('d-none');
                    btnText.textContent = originalText;
                    button.disabled = false;
                    showAlert('danger', 'An error occurred. Please try again.');
                });
            });
        });

        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-3`;
            alertDiv.setAttribute('role', 'alert');
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            document.querySelector('.container').insertBefore(alertDiv, document.querySelector('.container').firstChild);

            // Auto dismiss after 5 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
    </script>
@endsection

