@extends('layouts.admin')

@section('admin-title', 'Thêm danh mục mới')

@section('admin-content')
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-plus"></i> Thêm danh mục mới</h2>
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
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Tên danh mục *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                        value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Màu sác (hex)</label>
                        <div class="input-group">
                            <input type="color"
                                class="form-control form-control-color @error('color') is-invalid @enderror" name="color"
                                value="{{ old('color', '#3b82f6') }}">
                            <input type="text" class="form-control @error('color') is-invalid @enderror" name="color"
                                value="{{ old('color', '#3b82f6') }}" id="colorInput">
                        </div>
                        @error('color')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Biểu tượng (emoji)</label>
                        <input type="text" class="form-control @error('icon') is-invalid @enderror" name="icon"
                            value="{{ old('icon') }}" placeholder="📰">
                        @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Thứ tự</label>
                    <input type="number" class="form-control @error('order') is-invalid @enderror" name="order"
                        value="{{ old('order', 1) }}">
                    @error('order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Thêm danh mục
                    </button>
                    <a href="{{ route('admin.categories') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
