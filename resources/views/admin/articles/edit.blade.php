@extends('layouts.app')

@section('title', 'Admin - Sửa bài viết')

@section('content')
    <div class="container-fluid mt-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2><i class="fas fa-edit"></i> Sửa bài viết</h2>
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
                <form action="{{ route('admin.articles.update', $article) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Tiêu đề *</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" name="title"
                            value="{{ $article->title }}" required>
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
                                        {{ $article->category_id === $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tóm tắt</label>
                            <textarea class="form-control @error('summary') is-invalid @enderror" name="summary" rows="2">{{ $article->summary }}</textarea>
                            @error('summary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nội dung *</label>
                        <textarea class="form-control @error('content') is-invalid @enderror" name="content" rows="8" required>{{ $article->content }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_published" value="1"
                                    {{ $article->is_published ? 'checked' : '' }} id="is_published">
                                <label class="form-check-label" for="is_published">
                                    Xuất bản
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_featured" value="1"
                                    {{ $article->is_featured ? 'checked' : '' }} id="is_featured">
                                <label class="form-check-label" for="is_featured">
                                    Nổi bật
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu thay đổi
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
