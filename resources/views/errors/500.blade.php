@extends("layouts.app")

@section("title", "500 - L?i máy ch?")

@section("content")
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5 text-center">
                    <h1 class="display-1 text-danger">500</h1>
                    <h2 class="mb-3">L?i máy ch? n?i b?</h2>
                    <p class="text-muted mb-4">Có l?i x?y ra trên máy ch?. Vui lòng th? l?i sau ho?c liên h? v?i qu?n tr? viên.</p>
                    
                    <div class="mt-4">
                        <a href="{{ route("home") }}" class="btn btn-primary me-2">
                            <i class="fas fa-home"></i> V? trang ch?
                        </a>
                        <a href="javascript:history.back()" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay l?i
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
