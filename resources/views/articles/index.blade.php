@extends('layouts.app')

@section('title', 'Trang chủ - Tin Tức AI')

@section('content')
    <div class="container mt-4">

        <!-- Featured Articles Carousel -->
        @if ($featuredArticles->count() > 0)
            <div id="featuredCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    @foreach ($featuredArticles as $index => $article)
                        <button type="button" data-bs-target="#featuredCarousel" data-bs-slide-to="{{ $index }}"
                            class="{{ $index === 0 ? 'active' : '' }}"></button>
                    @endforeach
                </div>

                <div class="carousel-inner">
                    @foreach ($featuredArticles as $index => $article)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="featured-article"
                                style="background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('{{ $article->image_url ?: 'https://via.placeholder.com/1200x400' }}'); background-size: cover; background-position: center;">
                                <div class="featured-overlay">
                                    <span class="category-badge mb-2"
                                        style="background: {{ $article->category->color ?? '#3b82f6' }}">
                                        {{ $article->category->icon ?? '📰' }} {{ $article->category->name ?? 'Tin tức' }}
                                    </span>
                                    <h2 class="mb-3">
                                        <a href="{{ route('article.show', $article->slug) }}"
                                            class="text-white text-decoration-none">
                                            {{ $article->title }}
                                        </a>
                                    </h2>
                                    <p class="mb-3">
                                        {{ Str::limit($article->summary ?? strip_tags($article->content), 150) }}</p>
                                    <div class="d-flex align-items-center text-white-50">
                                        <small class="me-3">
                                            <i class="far fa-clock"></i>
                                            {{ $article->published_at?->diffForHumans() ?? 'N/A' }}
                                        </small>
                                        <small class="me-3">
                                            <i class="far fa-eye"></i> {{ number_format($article->views_count) }} lượt xem
                                        </small>
                                        @if ($article->source)
                                            <small>
                                                <i class="fas fa-rss"></i> {{ $article->source->name }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#featuredCarousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#featuredCarousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        @endif

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <h3 class="mb-4">
                    <i class="fas fa-newspaper text-primary"></i> Tin Mới Nhất
                </h3>

                <div class="row">
                    @foreach ($latestArticles as $article)
                        <div class="col-md-6 mb-4">
                            <div class="card article-card shadow-sm">
                                @if ($article->image_url)
                                    <img src="{{ $article->image_url }}" class="article-image"
                                        alt="{{ $article->title }}">
                                @else
                                    <div
                                        class="article-image bg-secondary d-flex align-items-center justify-content-center">
                                        <i class="fas fa-image fa-3x text-white-50"></i>
                                    </div>
                                @endif

                                <div class="card-body">
                                    @if ($article->category)
                                        <span class="category-badge mb-2"
                                            style="background: {{ $article->category->color }}">
                                            {{ $article->category->icon }} {{ $article->category->name }}
                                        </span>
                                    @endif

                                    <h5 class="card-title mt-2">
                                        <a href="{{ route('article.show', $article->slug) }}"
                                            class="text-decoration-none text-dark">
                                            {{ Str::limit($article->title, 80) }}
                                        </a>
                                    </h5>

                                    <p class="card-text text-muted small">
                                        {{ Str::limit($article->summary ?? strip_tags($article->content), 100) }}
                                    </p>

                                    <div class="d-flex justify-content-between align-items-center text-muted small">
                                        <span>
                                            <i class="far fa-clock"></i>
                                            {{ $article->published_at?->diffForHumans() ?? 'N/A' }}
                                        </span>
                                        <span>
                                            <i class="far fa-eye"></i> {{ number_format($article->views_count) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $latestArticles->links() }}
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Popular Articles -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-fire"></i> Xem Nhiều Nhất
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach ($popularArticles as $index => $article)
                            <div class="p-3 border-bottom">
                                <div class="d-flex">
                                    <span class="badge bg-danger me-2" style="font-size: 1rem;">{{ $index + 1 }}</span>
                                    <div>
                                        <a href="{{ route('article.show', $article->slug) }}"
                                            class="text-decoration-none text-dark">
                                            <strong>{{ Str::limit($article->title, 60) }}</strong>
                                        </a>
                                        <div class="text-muted small mt-1">
                                            <i class="far fa-eye"></i> {{ number_format($article->views_count) }} lượt xem
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Categories -->
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-folder"></i> Danh Mục
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach ($categories as $category)
                            <a href="{{ route('category.show', $category->slug) }}"
                                class="sidebar-category d-flex justify-content-between align-items-center text-decoration-none text-dark">
                                <span>
                                    <span style="font-size: 1.2rem;">{{ $category->icon }}</span>
                                    <strong class="ms-2">{{ $category->name }}</strong>
                                </span>
                                <span class="badge rounded-pill" style="background: {{ $category->color }}">
                                    {{ $category->published_articles_count }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
