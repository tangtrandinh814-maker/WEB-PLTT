@extends('layouts.app')

@section('title', $article->title . ' - Tin Tức AI')

@section('content')
    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-8">
                <article class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <!-- Category & Meta -->
                        <div class="mb-3">
                            @if ($article->category)
                                <a href="{{ route('category.show', $article->category->slug) }}" class="text-decoration-none">
                                    <span class="category-badge" style="background: {{ $article->category->color }}">
                                        {{ $article->category->icon }} {{ $article->category->name }}
                                    </span>
                                </a>
                            @endif

                            @if ($article->ai_confidence_score && $article->ai_confidence_score > 0.8)
                                <span class="badge bg-success ms-2">
                                    <i class="fas fa-robot"></i> AI:
                                    {{ number_format($article->ai_confidence_score * 100) }}%
                                </span>
                            @endif
                        </div>

                        <!-- Title -->
                        <h1 class="mb-3">{{ $article->title }}</h1>

                        <!-- Meta Information -->
                        <div class="d-flex flex-wrap text-muted small mb-4 pb-3 border-bottom">
                            <div class="me-4">
                                <i class="far fa-calendar"></i>
                                {{ $article->published_at?->format('d/m/Y H:i') ?? 'N/A' }}
                            </div>
                            <div class="me-4">
                                <i class="far fa-eye"></i>
                                {{ number_format($article->views_count) }} lượt xem
                            </div>
                            @if ($article->source)
                                <div class="me-4">
                                    <i class="fas fa-rss"></i>
                                    {{ $article->source->name }}
                                </div>
                            @endif
                            @if ($article->author)
                                <div>
                                    <i class="far fa-user"></i>
                                    {{ $article->author }}
                                </div>
                            @endif
                        </div>

                        <!-- Featured Image -->
                        @if ($article->image_url)
                            <div class="mb-4">
                                <img src="{{ $article->image_url }}" alt="{{ $article->title }}"
                                    class="img-fluid rounded">
                            </div>
                        @endif

                        <!-- Summary -->
                        @if ($article->summary)
                            <div class="alert alert-light border-start border-primary border-4 mb-4">
                                <strong><i class="fas fa-info-circle"></i> Tóm tắt:</strong>
                                <p class="mb-0 mt-2">{{ $article->summary }}</p>
                            </div>
                        @endif

                        <!-- Content -->
                        <div class="article-content">
                            {!! \App\Helpers\ArticleHelper::formatContent($article->content) !!}
                        </div>

                        <!-- Tags -->
                        @if ($article->tags && count($article->tags) > 0)
                            <div class="mt-4 pt-3 border-top">
                                <strong class="text-muted">Tags:</strong>
                                @foreach ($article->tags as $tag)
                                    <span class="badge bg-secondary me-1">#{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif

                        <!-- Source Link -->
                        @if ($article->original_url)
                            <div class="mt-3">
                                <a href="{{ $article->original_url }}" target="_blank"
                                    class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-external-link-alt"></i> Xem bài gốc
                                </a>
                            </div>
                        @endif

                        <!-- Share Buttons -->
                        <div class="mt-4 pt-3 border-top">
                            <strong class="text-muted me-3">Chia sẻ:</strong>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('article.show', $article->slug)) }}"
                                target="_blank" class="btn btn-primary btn-sm">
                                <i class="fab fa-facebook"></i> Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('article.show', $article->slug)) }}&text={{ urlencode($article->title) }}"
                                target="_blank" class="btn btn-info btn-sm text-white">
                                <i class="fab fa-twitter"></i> Twitter
                            </a>
                        </div>
                    </div>
                </article>

                <!-- Related Articles -->
                @if ($relatedArticles->count() > 0)
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-newspaper"></i> Bài Viết Liên Quan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach ($relatedArticles as $related)
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex">
                                            @if ($related->image_url)
                                                <img src="{{ $related->image_url }}" alt="{{ $related->title }}"
                                                    class="rounded me-3"
                                                    style="width: 80px; height: 80px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <a href="{{ route('article.show', $related->slug) }}"
                                                    class="text-decoration-none text-dark">
                                                    <strong>{{ Str::limit($related->title, 60) }}</strong>
                                                </a>
                                                <div class="text-muted small mt-1">
                                                    <i class="far fa-clock"></i>
                                                    {{ $related->published_at?->diffForHumans() ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Latest in Category -->
                @if ($article->category)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header" style="background: {{ $article->category->color }}; color: white;">
                            <h5 class="mb-0">
                                {{ $article->category->icon }} Tin {{ $article->category->name }}
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            @foreach ($article->category->publishedArticles()->limit(5)->get() as $categoryArticle)
                                @if ($categoryArticle->id !== $article->id)
                                    <div class="p-3 border-bottom">
                                        <a href="{{ route('article.show', $categoryArticle->slug) }}"
                                            class="text-decoration-none text-dark">
                                            <strong>{{ Str::limit($categoryArticle->title, 70) }}</strong>
                                        </a>
                                        <div class="text-muted small mt-1">
                                            <i class="far fa-clock"></i>
                                            {{ $categoryArticle->published_at?->diffForHumans() ?? 'N/A' }}
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Ad Space -->
                <div class="card shadow-sm bg-light text-center">
                    <div class="card-body" style="min-height: 300px;">
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <div>
                                <i class="fas fa-ad fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Không gian quảng cáo</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .article-content {
            font-size: 1.1rem;
            line-height: 1.8;
            text-align: justify;
        }

        .article-content p {
            margin-bottom: 1.5rem;
        }
    </style>
@endpush
