@extends('layouts.app')

@section('title', 'Admin - Quản lý bài viết')

@section('content')
    <div class="container-fluid mt-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2><i class="fas fa-newspaper"></i> Quản lý bài viết</h2>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.articles.create') }}" class="btn btn-primary me-2">
                    <i class="fas fa-plus"></i> Thêm bài viết
                </a>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Danh sách bài viết ({{ $articles->total() }} bài)</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="10%">Hình ảnh</th>
                            <th width="25%">Tiêu đề</th>
                            <th width="12%">Danh mục</th>
                            <th width="12%">Nguồn</th>
                            <th width="12%">Ngày tạo</th>
                            <th width="24%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($articles as $article)
                            <tr>
                                <td>{{ $article->id }}</td>
                                <td>
                                    @if ($article->image_url)
                                        <img src="{{ $article->image_url }}" alt="{{ $article->title }}"
                                            style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    @else
                                        <div
                                            style="width: 50px; height: 50px; background: #e2e8f0; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ Str::limit($article->title, 35) }}</strong><br>
                                    <small class="text-muted">
                                        @if ($article->is_published)
                                            <span class="badge bg-success">Đã xuất bản</span>
                                        @else
                                            <span class="badge bg-warning">Nháp</span>
                                        @endif
                                        @if ($article->is_featured)
                                            <span class="badge bg-info">Nổi bật</span>
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    @if ($article->category)
                                        <span class="badge" style="background: {{ $article->category->color }}">
                                            {{ $article->category->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($article->source)
                                        {{ $article->source->name }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $article->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <form action="{{ route('admin.articles.delete', $article) }}" method="POST"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Bạn chắc chắn?')">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Chưa có bài viết nào
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $articles->links() }}
            </div>
        </div>
    </div>
@endsection
