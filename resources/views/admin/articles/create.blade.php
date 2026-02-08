@extends('layouts.app')

@section('title', 'Thêm bài viết mới')

@section('content')
    <div class="container-fluid mt-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2><i class="fas fa-plus"></i> Thêm bài viết mới</h2>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('admin.articles') }}" class="btn btn-secondary">
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
                <form action="{{ route('admin.articles.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Tiêu đề bài viết *</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" name="title"
                            value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Danh mục *</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" name="category_id"
                                required>
                                <option value="">-- Chọn danh mục --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nguồn tin</label>
                            <select class="form-select @error('source_id') is-invalid @enderror" name="source_id">
                                <option value="">-- Chọn nguồn tin --</option>
                                @foreach ($sources as $source)
                                    <option value="{{ $source->id }}"
                                        {{ old('source_id') == $source->id ? 'selected' : '' }}>
                                        {{ $source->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('source_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tóm tắt</label>
                        <textarea class="form-control @error('summary') is-invalid @enderror" name="summary" rows="2">{{ old('summary') }}</textarea>
                        @error('summary')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nội dung bài viết *</label>
                        <textarea class="form-control @error('content') is-invalid @enderror" name="content" rows="10" required>{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hình ảnh (URL)</label>
                            <input type="url" class="form-control @error('image_url') is-invalid @enderror"
                                name="image_url" value="{{ old('image_url') }}">
                            @error('image_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tác giả</label>
                            <input type="text" class="form-control @error('author') is-invalid @enderror" name="author"
                                value="{{ old('author') }}">
                            @error('author')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_published" value="1"
                                    {{ old('is_published') ? 'checked' : 'checked' }} id="is_published">
                                <label class="form-check-label" for="is_published">
                                    Xuất bản ngay
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_featured" value="1"
                                    {{ old('is_featured') ? 'checked' : '' }} id="is_featured">
                                <label class="form-check-label" for="is_featured">
                                    Đánh dấu nổi bật
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Thêm bài viết
                        </button>
                        <a href="{{ route('admin.articles') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
