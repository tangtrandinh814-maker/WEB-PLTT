<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tin Tức AI - Phân loại tự động')</title>

    <!-- Google Fonts - Professional Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap"
        rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Professional Color & Styling CSS -->
    <link rel="stylesheet" href="{{ asset('css/professional.css') }}">

    <style>
        /* Apply Google Fonts */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: #f8fafc;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        }

        /* Custom overrides for professional look */
        .article-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 1rem;
            overflow: hidden;
            height: 100%;
            background: white;
        }

        .article-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 30px rgba(0, 0, 0, 0.12);
        }

        .article-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .featured-article {
            position: relative;
            height: 450px;
            border-radius: 1.5rem;
            overflow: hidden;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .featured-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.85), transparent);
            padding: 3rem 2rem;
            color: white;
        }

        footer {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #cbd5e1;
            padding: 3rem 0 1rem;
            margin-top: 5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top"
        style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <div class="container">
            <a class="navbar-brand fw-800" href="{{ route('home') }}" style="font-size: 1.4rem;">
                <i class="fas fa-newspaper me-2"></i> AiNews
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @foreach (\App\Models\Category::active()->ordered()->limit(6)->get() as $cat)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('category.show', $cat->slug) }}"
                                style="color: rgba(255,255,255,0.85); font-weight: 500;">
                                {{ $cat->icon }} <span class="d-none d-lg-inline">{{ $cat->name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                <form class="d-flex me-3 mb-3 mb-lg-0" action="{{ route('search') }}" method="GET">
                    <input class="form-control form-control-sm" type="search" name="q" placeholder="Tìm kiếm..."
                        value="{{ request('q') }}"
                        style="border-radius: 0.5rem 0 0 0.5rem; background: rgba(255,255,255,0.95);">
                    <button class="btn btn-light btn-sm" type="submit" style="border-radius: 0 0.5rem 0.5rem 0;">
                        <i class="fas fa-search"></i>
                    </button>
                </form>

                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false" style="color: rgba(255,255,255,0.85);">
                                <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-tachometer-alt"></i> Dashboard
                                    </a></li>
                                @if (Auth::user()->isAdmin())
                                    <li><a class="dropdown-item" href="{{ route('admin.articles') }}">
                                            <i class="fas fa-newspaper"></i> Quản lý bài viết
                                        </a></li>
                                @endif
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
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
                            <a class="nav-link" href="{{ route('login') }}"
                                style="color: rgba(255,255,255,0.85); font-weight: 500;">Đăng nhập</a>
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
                        @foreach (\App\Models\Category::active()->ordered()->limit(5)->get() as $cat)
                            <li class="mb-2">
                                <a href="{{ route('category.show', $cat->slug) }}"
                                    class="text-decoration-none text-light">
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
