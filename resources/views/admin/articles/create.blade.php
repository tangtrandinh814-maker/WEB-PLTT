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
                        <div class="input-group">
                            <input type="text" class="form-control @error('title') is-invalid @enderror" name="title"
                                id="title" value="{{ old('title') }}" required>
                            <button class="btn btn-outline-info" type="button" id="aiClassifyBtn">
                                <i class="fas fa-wand-magic-wand"></i> Phân loại AI
                            </button>
                        </div>
                        @error('title')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- AI Classification Result -->
                    <div id="aiResultAlert" class="alert alert-info alert-dismissible fade show" role="alert"
                        style="display:none;">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-2"><i class="fas fa-sparkles"></i> Kết quả phân loại AI</h5>
                                <div id="aiResultContent"></div>
                            </div>
                            <div class="col-md-4 text-end">
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        </div>
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

    <script>
        document.getElementById('aiClassifyBtn').addEventListener('click', async function() {
            const title = document.getElementById('title').value;
            const content = document.querySelector('textarea[name="content"]').value;

            // Validate inputs
            if (!title.trim() || content.trim().length < 20) {
                alert('Vui lòng nhập tiêu đề và nội dung ít nhất 20 ký tự');
                return;
            }

            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang phân loại...';

            try {
                const response = await fetch('{{ route('admin.articles.classify') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        title,
                        content
                    })
                });

                const data = await response.json();

                if (data.success && data.data.category_id) {
                    // Update form with AI result
                    document.querySelector('select[name="category_id"]').value = data.data.category_id;

                    // Show result alert
                    const resultAlert = document.getElementById('aiResultAlert');
                    const resultContent = document.getElementById('aiResultContent');

                    resultContent.innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>Danh mục:</strong>
                                    <span class="badge" style="background-color: ${data.data.category_color}">
                                        ${data.data.category_name}
                                    </span>
                                </p>
                                <p class="mb-2">
                                    <strong>Độ tin cậy:</strong>
                                    <span class="badge bg-success">${data.data.confidence_score}%</span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                ${data.data.tags.length > 0 ? `
                                        <p class="mb-2">
                                            <strong>Từ khóa:</strong>
                                            ${data.data.tags.map(tag => `<span class="badge bg-secondary">${tag}</span>`).join(' ')}
                                        </p>
                                    ` : ''}
                            </div>
                        </div>
                        ${data.data.summary ? `
                                <p class="mt-2 mb-0">
                                    <strong>Tóm tắt gợi ý:</strong><br>
                                    ${data.data.summary}
                                </p>
                            ` : ''}
                    `;

                    resultAlert.style.display = 'block';
                } else {
                    alert('Không thể phân loại bài viết. Vui lòng chọn danh mục thủ công.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Lỗi khi gửi yêu cầu: ' + error.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-wand-magic-wand"></i> Phân loại AI';
            }
        });
    </script>
