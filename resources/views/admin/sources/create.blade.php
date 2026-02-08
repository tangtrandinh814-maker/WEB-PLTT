@extends('layouts.app')

@section('title', 'Thêm nguồn tin mới')

@section('content')
    <div class="container-fluid mt-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2><i class="fas fa-plus"></i> Thêm nguồn tin mới</h2>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('admin.sources') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.sources.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Tên nguồn tin *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                            value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">URL chính *</label>
                        <input type="url" class="form-control @error('url') is-invalid @enderror" name="url"
                            value="{{ old('url') }}" placeholder="https://example.com" required>
                        @error('url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">URL RSS Feed</label>
                        <input type="url" class="form-control @error('rss_url') is-invalid @enderror" name="rss_url"
                            value="{{ old('rss_url') }}" placeholder="https://example.com/rss">
                        @error('rss_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Logo URL</label>
                        <input type="url" class="form-control @error('logo') is-invalid @enderror" name="logo"
                            value="{{ old('logo') }}" placeholder="https://example.com/logo.png">
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tần suất crawl (phút)</label>
                        <input type="number" class="form-control @error('crawl_frequency') is-invalid @enderror"
                            name="crawl_frequency" value="{{ old('crawl_frequency', 60) }}">
                        <small class="text-muted">Mặc định: 60 phút</small>
                        @error('crawl_frequency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Thêm nguồn tin
                        </button>
                        <a href="{{ route('admin.sources') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
