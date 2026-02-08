@extends('layouts.app')

@section('title', $category->name)

@section('content')
    <div class="container my-5">
        <div class="mb-4">
            <h1>{{ $category->name }}</h1>
            @if ($category->description)
                <p class="text-muted">{{ $category->description }}</p>
            @endif
        </div>

        <div class="row">
            <div class="col-md-9">
                <div class="articles-grid">
                    @forelse($articles as $article)
                        <div class="card mb-3">
                            @if ($article->image_url)
                                <img src="{{ $article->image_url }}" class="card-img-top" alt="{{ $article->title }}">
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="{{ route('article.show', $article->slug) }}">{{ $article->title }}</a>
                                </h5>
                                <p class="card-text">{{ Str::limit($article->summary ?? $article->content, 150) }}</p>
                                <small class="text-muted">
                                    {{ $article->published_at?->format('d/m/Y') }} •
                                    {{ $article->views_count }} views
                                </small>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info">
                            No articles found in this category.
                        </div>
                    @endforelse
                </div>

                {{ $articles->links() }}
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Categories</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach ($categories as $cat)
                            <a href="{{ route('category.show', $cat->slug) }}"
                                class="list-group-item list-group-item-action {{ $cat->id === $category->id ? 'active' : '' }}">
                                {{ $cat->name }}
                                <span class="badge bg-primary float-end">{{ $cat->published_articles_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
