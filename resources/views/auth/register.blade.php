@extends('layouts.app')

@section('title', 'Đăng ký tài khoản - AiNews')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                        style="width: 70px; height: 70px; background: linear-gradient(135deg, #0284c7, #0369a1);">
                        <i class="fas fa-user-plus text-white fa-2x"></i>
                    </div>
                    <h2 class="fw-bold mb-1">Tạo tài khoản mới</h2>
                    <p class="text-muted">Đăng ký để theo dõi tin tức yêu thích của bạn</p>
                </div>

                <div class="card border-0 shadow-lg" style="border-radius: 1rem;">
                    <div class="card-body p-4 p-md-5">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            {{-- Họ tên --}}
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">
                                    <i class="fas fa-user text-primary me-1"></i> Họ và tên
                                </label>
                                <input id="name" type="text"
                                    class="form-control form-control-lg @error('name') is-invalid @enderror" name="name"
                                    value="{{ old('name') }}" placeholder="Nhập họ và tên của bạn" required
                                    autocomplete="name" autofocus style="border-radius: 0.75rem;">
                                @error('name')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="fas fa-envelope text-primary me-1"></i> Địa chỉ Email
                                </label>
                                <input id="email" type="email"
                                    class="form-control form-control-lg @error('email') is-invalid @enderror" name="email"
                                    value="{{ old('email') }}" placeholder="example@email.com" required
                                    autocomplete="email" style="border-radius: 0.75rem;">
                                @error('email')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            {{-- Mật khẩu --}}
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="fas fa-lock text-primary me-1"></i> Mật khẩu
                                </label>
                                <div class="position-relative">
                                    <input id="password" type="password"
                                        class="form-control form-control-lg @error('password') is-invalid @enderror"
                                        name="password" placeholder="Tối thiểu 8 ký tự" required autocomplete="new-password"
                                        style="border-radius: 0.75rem;">
                                    <button type="button"
                                        class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted pe-3"
                                        onclick="togglePassword('password', this)" style="text-decoration: none;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            {{-- Xác nhận mật khẩu --}}
                            <div class="mb-4">
                                <label for="password-confirm" class="form-label fw-semibold">
                                    <i class="fas fa-lock text-primary me-1"></i> Xác nhận mật khẩu
                                </label>
                                <div class="position-relative">
                                    <input id="password-confirm" type="password" class="form-control form-control-lg"
                                        name="password_confirmation" placeholder="Nhập lại mật khẩu" required
                                        autocomplete="new-password" style="border-radius: 0.75rem;">
                                    <button type="button"
                                        class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted pe-3"
                                        onclick="togglePassword('password-confirm', this)" style="text-decoration: none;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Nút đăng ký --}}
                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold"
                                style="border-radius: 0.75rem; background: linear-gradient(135deg, #0284c7, #0369a1); border: none; padding: 0.8rem;">
                                <i class="fas fa-user-plus me-2"></i> Đăng ký
                            </button>
                        </form>

                        {{-- Đường dẫn đăng nhập --}}
                        <div class="text-center mt-4 pt-3" style="border-top: 1px solid #e2e8f0;">
                            <p class="text-muted mb-0">
                                Đã có tài khoản?
                                <a href="{{ route('login') }}" class="fw-semibold text-decoration-none"
                                    style="color: #0284c7;">
                                    Đăng nhập ngay <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function togglePassword(inputId, btn) {
                const input = document.getElementById(inputId);
                const icon = btn.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            }
        </script>
    @endpush
@endsection
