<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tin Tức AI - Phân loại tự động')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #3b82f6;
            --secondary-color: #64748b;
            --danger-color: #ef4444;
            --success-color: #10b981;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1e293b;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
        }

        .category-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: white;
        }

        .article-card {
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            border-radius: 0.5rem;
            overflow: hidden;
            height: 100%;
        }

        .article-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .article-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .featured-article {
            position: relative;
            height: 400px;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .featured-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            padding: 2rem;
            color: white;
        }

        .sidebar-category {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: background 0.2s;
        }

        .sidebar-category:hover {
            background: #f1f5f9;
        }

        .trending-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        footer {
            background: #1e293b;
            color: #cbd5e1;
            padding: 3rem 0;
            margin-top: 4rem;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-newspaper"></i> Tin Tức AI
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @foreach(\App\Models\Category::active()->ordered()->limit(6)->get() as $cat)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('category.show', $cat->slug) }}">
                            {{ $cat->icon }} {{ $cat->name }}
                        </a>
                    </li>
                    @endforeach
                </ul>

                <form class="d-flex me-3" action="{{ route('search') }}" method="GET">
                    <input class="form-control me-2" type="search" name="q" placeholder="Tìm kiếm..." value="{{ request('q') }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>

                <ul class="navbar-nav">
                    @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                    @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Đăng nhập</a>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    @yield('content')

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5 class="text-white mb-3">Về chúng tôi</h5>
                    <p>Hệ thống tin tức tự động phân loại bằng AI, mang đến tin tức chính xác và nhanh chóng.</p>
                </div>
                <div class="col-md-4">
                    <h5 class="text-white mb-3">Danh mục</h5>
                    <ul class="list-unstyled">
                        @foreach(\App\Models\Category::active()->ordered()->limit(5)->get() as $cat)
                        <li class="mb-2">
                            <a href="{{ route('category.show', $cat->slug) }}" class="text-decoration-none text-light">
                                {{ $cat->icon }} {{ $cat->name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="text-white mb-3">Liên hệ</h5>
                    <p><i class="fas fa-envelope"></i> contact@news-ai.com</p>
                    <p><i class="fas fa-phone"></i> +84 123 456 789</p>
                    <div class="mt-3">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-youtube fa-lg"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4" style="border-color: #475569;">
            <div class="text-center">
                <p class="mb-0">&copy; 2025 Tin Tức AI. Powered by Laravel & OpenAI.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
