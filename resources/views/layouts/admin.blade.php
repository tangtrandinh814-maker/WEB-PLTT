@extends('layouts.app')

@section('title')
    @yield('admin-title', 'Admin - Tin Tức AI')
@endsection

@section('content')
    <div class="container-fluid mt-0">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 bg-dark text-white p-3 admin-sidebar" style="min-height: calc(100vh - 120px);">
                <h5 class="mb-4"><i class="fas fa-tachometer-alt"></i> Admin Panel</h5>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.dashboard') }}"
                            class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active bg-primary rounded' : '' }}">
                            <i class="fas fa-home me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.articles') }}"
                            class="nav-link text-white {{ request()->routeIs('admin.articles*') ? 'active bg-primary rounded' : '' }}">
                            <i class="fas fa-newspaper me-2"></i> Bài viết
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.categories') }}"
                            class="nav-link text-white {{ request()->routeIs('admin.categories*') ? 'active bg-primary rounded' : '' }}">
                            <i class="fas fa-folder me-2"></i> Danh mục
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.sources') }}"
                            class="nav-link text-white {{ request()->routeIs('admin.sources*') ? 'active bg-primary rounded' : '' }}">
                            <i class="fas fa-rss me-2"></i> Nguồn tin
                        </a>
                    </li>
                    <li class="nav-item mb-2 border-top border-secondary pt-2">
                        <a href="{{ route('admin.test-ai') }}"
                            class="nav-link text-white {{ request()->routeIs('admin.test-ai*') ? 'active bg-primary rounded' : '' }}">
                            <i class="fas fa-robot me-2"></i> Test AI
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <form action="{{ route('admin.crawl') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm w-100">
                                <i class="fas fa-sync me-1"></i> Crawl ngay
                            </button>
                        </form>
                    </li>
                    <li class="nav-item mt-3 border-top border-secondary pt-3">
                        <a href="{{ route('home') }}" class="nav-link text-white-50">
                            <i class="fas fa-arrow-left me-2"></i> Về trang chủ
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
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

                @yield('admin-content')
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .admin-sidebar .nav-link {
                padding: 0.5rem 0.75rem;
                font-size: 0.9rem;
                transition: all 0.2s ease;
                border-radius: 0.375rem;
            }

            .admin-sidebar .nav-link:hover {
                background-color: rgba(255, 255, 255, 0.1);
            }

            .admin-sidebar .nav-link.active {
                font-weight: 600;
            }
        </style>
    @endpush
@endsection
