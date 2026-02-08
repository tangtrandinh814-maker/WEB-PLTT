@extends('layouts.app')

@section('title', 'Sửa nguồn tin - ' . $source->name)

@section('content')
    <div class="container-fluid mt-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2><i class="fas fa-edit"></i> Sửa nguồn tin: {{ $source->name }}</h2>
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
                <form action="{{ route('admin.sources.update', $source) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Tên nguồn tin *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                            value="{{ $source->name }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">URL chính *</label>
                        <input type="url" class="form-control @error('url') is-invalid @enderror" name="url"
                            value="{{ $source->url }}" required>
                        @error('url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">URL RSS Feed</label>
                        <input type="url" class="form-control @error('rss_url') is-invalid @enderror" name="rss_url"
                            value="{{ $source->rss_url }}">
                        @error('rss_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Logo URL</label>
                        <input type="url" class="form-control @error('logo') is-invalid @enderror" name="logo"
                            value="{{ $source->logo }}">
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tần suất crawl (phút)</label>
                            <input type="number" class="form-control @error('crawl_frequency') is-invalid @enderror"
                                name="crawl_frequency" value="{{ $source->crawl_frequency ?? 60 }}">
                            @error('crawl_frequency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                    {{ $source->is_active ? 'checked' : '' }} id="is_active">
                                <label class="form-check-label" for="is_active">
                                    Kích hoạt
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Cập nhật nguồn tin
                        </button>
                        <a href="{{ route('admin.sources') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                        <form action="{{ route('admin.sources.delete', $source) }}" method="POST"
                            style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn chắc chắn?')">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </form>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
