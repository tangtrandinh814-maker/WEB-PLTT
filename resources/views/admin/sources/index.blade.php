@extends('layouts.app')

@section('title', 'Admin - Quản lý nguồn tin')

@section('content')
    <div class="container-fluid mt-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2><i class="fas fa-rss"></i> Quản lý nguồn tin</h2>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.sources.create') }}" class="btn btn-primary me-2">
                    <i class="fas fa-plus"></i> Thêm nguồn tin
                </a>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Danh sách nguồn tin ({{ $sources->count() }} nguồn)</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="20%">Tên nguồn</th>
                            <th width="20%">URL</th>
                            <th width="15%">Bài viết</th>
                            <th width="15%">Trạng thái</th>
                            <th width="15%">Crawl lần cuối</th>
                            <th width="10%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sources as $source)
                            <tr>
                                <td>{{ $source->id }}</td>
                                <td>
                                    <strong>{{ $source->name }}</strong>
                                </td>
                                <td>
                                    <a href="{{ $source->url }}" target="_blank" class="text-decoration-none">
                                        {{ Str::limit($source->url, 40) }} <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $source->articles_count }}</span> bài viết
                                </td>
                                <td>
                                    @if ($source->is_active)
                                        <span class="badge bg-success">Kích hoạt</span>
                                    @else
                                        <span class="badge bg-secondary">Tắt</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($source->last_crawled_at)
                                        {{ $source->last_crawled_at?->diffForHumans() ?? 'N/A' }}
                                    @else
                                        <span class="text-muted">Chưa crawl</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.sources.edit', $source) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <form action="{{ route('admin.sources.delete', $source) }}" method="POST"
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
                                <td colspan="7" class="text-center text-muted py-4">
                                    Chưa có nguồn tin nào
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
