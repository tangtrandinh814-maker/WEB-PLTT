@extends('layouts.app')

@section('title', 'Admin - Quản lý danh mục')

@section('content')
    <div class="container-fluid mt-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2><i class="fas fa-folder"></i> Quản lý danh mục</h2>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary me-2">
                    <i class="fas fa-plus"></i> Thêm danh mục
                </a>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        <div class="row">
            @forelse($categories as $category)
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <span class="badge" style="background: {{ $category->color }}">{{ $category->icon }}</span>
                                {{ $category->name }}
                            </h5>
                            <p class="card-text text-muted">
                                {{ $category->description ?? 'Không có mô tả' }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-newspaper"></i> {{ $category->articles_count }} bài viết
                                    <span class="ms-2">
                                        <i class="fas fa-check-circle text-success"></i>
                                        {{ $category->published_articles_count }} xuất bản
                                    </span>
                                </small>
                                @if ($category->is_active)
                                    <span class="badge bg-success">Kích hoạt</span>
                                @else
                                    <span class="badge bg-secondary">Tắt</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer bg-light d-flex gap-2">
                            <a href="{{ route('admin.categories.edit', $category) }}"
                                class="btn btn-sm btn-primary flex-grow-1">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            <form action="{{ route('admin.categories.delete', $category) }}" method="POST"
                                style="flex-grow: 1;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger w-100"
                                    onclick="return confirm('Bạn chắc chắn?')">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        Chưa có danh mục nào
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
