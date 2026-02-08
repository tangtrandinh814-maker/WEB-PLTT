@extends('layouts.app')

@section('title', 'Sửa danh mục - ' . $category->name)

@section('content')
    <div class="container-fluid mt-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2><i class="fas fa-edit"></i> Sửa danh mục: {{ $category->name }}</h2>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('admin.categories') }}" class="btn btn-secondary">
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
                <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Tên danh mục *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                            value="{{ $category->name }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ $category->description }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Màu sác (hex)</label>
                            <div class="input-group">
                                <input type="color"
                                    class="form-control form-control-color @error('color') is-invalid @enderror"
                                    name="color" value="{{ $category->color ?? '#3b82f6' }}">
                                <input type="text" class="form-control @error('color') is-invalid @enderror"
                                    name="color" value="{{ $category->color ?? '#3b82f6' }}">
                            </div>
                            @error('color')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Biểu tượng (emoji)</label>
                            <input type="text" class="form-control @error('icon') is-invalid @enderror" name="icon"
                                value="{{ $category->icon ?? '' }}" placeholder="📰">
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Thứ tự</label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" name="order"
                                value="{{ $category->order ?? 1 }}">
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                    {{ $category->is_active ? 'checked' : '' }} id="is_active">
                                <label class="form-check-label" for="is_active">
                                    Kích hoạt
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Cập nhật danh mục
                        </button>
                        <a href="{{ route('admin.categories') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                        <form action="{{ route('admin.categories.delete', $category) }}" method="POST"
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
