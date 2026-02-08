@extends('layouts.app')

@section('title', 'Search Results')

@section('content')
    <div class="container my-5">
        <div class="mb-4">
            <h1>Search Results</h1>
            @if ($query)
                <p class="text-muted">Results for: <strong>{{ $query }}</strong></p>
            @endif
        </div>

        <div class="row">
            <div class="col-md-9">
                @forelse($articles as $article)
                    <div class="card mb-3">
                        @if ($article->image_url)
                            <img src="{{ $article->image_url }}" class="card-img-top" alt="{{ $article->title }}"
                                style="max-height: 300px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="{{ route('article.show', $article->slug) }}">{{ $article->title }}</a>
                            </h5>
                            <p class="card-text">{{ Str::limit($article->summary ?? $article->content, 200) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <span class="badge bg-secondary">{{ $article->category?->name }}</span>
                                    <span class="badge bg-info">{{ $article->source?->name }}</span>
                                </small>
                                <small class="text-muted">
                                    {{ $article->published_at?->format('d/m/Y') }} •
                                    {{ $article->views_count }} views
                                </small>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info">
                        @if ($query)
                            No articles found for "{{ $query }}". Try a different search term.
                        @else
                            Enter a search term to find articles.
                        @endif
                    </div>
                @endforelse

                {{ $articles->links() }}
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Search Again</h5>
                        <form action="{{ route('search') }}" method="GET">
                            <div class="input-group">
                                <input type="text" class="form-control" name="q" placeholder="Search articles..."
                                    value="{{ $query }}">
                                <button class="btn btn-primary" type="submit">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
