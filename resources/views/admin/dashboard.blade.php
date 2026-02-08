@extends('layouts.app')

@section('title', 'Admin Dashboard - Tin Tức AI')

@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 bg-dark text-white p-3" style="min-height: calc(100vh - 120px);">
                <h5 class="mb-4"><i class="fas fa-tachometer-alt"></i> Admin Panel</h5>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link text-white active">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.articles') }}" class="nav-link text-white">
                            <i class="fas fa-newspaper"></i> Bài viết
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.categories') }}" class="nav-link text-white">
                            <i class="fas fa-folder"></i> Danh mục
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.sources') }}" class="nav-link text-white">
                            <i class="fas fa-rss"></i> Nguồn tin
                        </a>
                    </li>
                    <li class="nav-item mb-2 border-top pt-2">
                        <a href="{{ route('admin.test-ai') }}" class="nav-link text-white">
                            <i class="fas fa-robot"></i> Test AI
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <form action="{{ route('admin.crawl') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm w-100">
                                <i class="fas fa-sync"></i> Crawl ngay
                            </button>
                        </form>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-10">
                <h2 class="mb-4">Dashboard</h2>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary shadow">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-newspaper"></i> Tổng bài viết</h5>
                                <h2 class="mb-0">{{ number_format($stats['total_articles']) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success shadow">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-check-circle"></i> Đã xuất bản</h5>
                                <h2 class="mb-0">{{ number_format($stats['published_articles']) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning shadow">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-calendar-day"></i> Hôm nay</h5>
                                <h2 class="mb-0">{{ number_format($stats['today_articles']) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info shadow">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-eye"></i> Lượt xem</h5>
                                <h2 class="mb-0">{{ number_format($stats['total_views']) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Recent Articles -->
                    <div class="col-md-6">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-newspaper"></i> Bài viết mới nhất</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tiêu đề</th>
                                                <th>Danh mục</th>
                                                <th>Thời gian</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($recentArticles as $article)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('article.show', $article->slug) }}"
                                                            target="_blank">
                                                            {{ Str::limit($article->title, 40) }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        @if ($article->category)
                                                            <span class="badge"
                                                                style="background: {{ $article->category->color }}">
                                                                {{ $article->category->name }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-muted small">
                                                        {{ $article->created_at?->diffForHumans() ?? 'N/A' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Articles -->
                    <div class="col-md-6">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0"><i class="fas fa-fire"></i> Bài viết xem nhiều</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tiêu đề</th>
                                                <th>Danh mục</th>
                                                <th>Lượt xem</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($topArticles as $article)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('article.show', $article->slug) }}"
                                                            target="_blank">
                                                            {{ Str::limit($article->title, 40) }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        @if ($article->category)
                                                            <span class="badge"
                                                                style="background: {{ $article->category->color }}">
                                                                {{ $article->category->name }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <strong>{{ number_format($article->views_count) }}</strong>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Statistics -->
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Thống kê theo danh mục</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Danh mục</th>
                                        <th class="text-center">Tổng bài viết</th>
                                        <th class="text-center">Đã xuất bản</th>
                                        <th class="text-center">Tỷ lệ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categoryStats as $category)
                                        <tr>
                                            <td>
                                                <span style="font-size: 1.2rem;">{{ $category->icon }}</span>
                                                <strong class="ms-2">{{ $category->name }}</strong>
                                            </td>
                                            <td class="text-center">{{ number_format($category->articles_count) }}</td>
                                            <td class="text-center">
                                                {{ number_format($category->published_articles_count) }}</td>
                                            <td class="text-center">
                                                @php
                                                    $percentage =
                                                        $category->articles_count > 0
                                                            ? round(
                                                                ($category->published_articles_count /
                                                                    $category->articles_count) *
                                                                    100,
                                                            )
                                                            : 0;
                                                @endphp
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" role="progressbar"
                                                        style="width: {{ $percentage }}%; background: {{ $category->color }}"
                                                        aria-valuenow="{{ $percentage }}" aria-valuemin="0"
                                                        aria-valuemax="100">
                                                        {{ $percentage }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
