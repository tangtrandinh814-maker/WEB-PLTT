@extends('layouts.admin')

@section('admin-title', 'Admin - Test AI Classification')

@section('admin-content')
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-robot"></i> Test AI Classification</h2>
            <p class="text-muted">Nhập tiêu đề và nội dung để test hệ thống phân loại AI</p>
        </div>
    </div>

    <div class="row">
        <!-- Input Form -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Nhập thông tin bài viết</h5>
                </div>
                <div class="card-body">
                    <form id="aiTestForm">
                        <div class="mb-3">
                            <label class="form-label">Tiêu đề bài viết *</label>
                            <input type="text" class="form-control" id="title" name="title"
                                placeholder="Ví dụ: Hôm nay là ngày đẹp trời..." required>
                            <small class="text-muted">Nhập tiêu đề bài viết cần phân loại</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nội dung bài viết *</label>
                            <textarea class="form-control" id="content" name="content" rows="8"
                                placeholder="Nhập nội dung bài viết (tối thiểu 50 ký tự)..." required></textarea>
                            <small class="text-muted">Nhập nội dung hoặc tóm tắt của bài viết</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-magic"></i> Test AI Classification
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Test Examples -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Ví dụ nhanh</h5>
                </div>
                <div class="btn-group-vertical w-100" role="group">
                    <button type="button" class="btn btn-light text-start" onclick="loadExample(1)">
                        <strong>🏋️ Thể thao</strong><br>
                        <small>Nội dung về bóng đá, bóng chuyền...</small>
                    </button>
                    <button type="button" class="btn btn-light text-start border-top" onclick="loadExample(2)">
                        <strong>💻 Công nghệ</strong><br>
                        <small>Nội dung về AI, Python, Web...</small>
                    </button>
                    <button type="button" class="btn btn-light text-start border-top" onclick="loadExample(3)">
                        <strong>💼 Kinh doanh</strong><br>
                        <small>Nội dung về tài chính, doanh nghiệp...</small>
                    </button>
                    <button type="button" class="btn btn-light text-start border-top" onclick="loadExample(4)">
                        <strong>🏥 Sức khỏe</strong><br>
                        <small>Nội dung về y tế, sức khỏe...</small>
                    </button>
                </div>
            </div>
        </div>

        <!-- Result Display -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Kết quả phân loại</h5>
                </div>
                <div class="card-body" id="resultContent" style="min-height: 400px;">
                    <div class="text-center text-muted py-5">
                        <p><i class="fas fa-arrow-left"></i></p>
                        <p>Nhập thông tin bài viết và nhấn "Test AI Classification" để xem kết quả</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load example data
        const examples = {
            1: {
                title: "Việt Nam vô địch bóng chuyền nữ tại SEA Games 2026",
                content: `Đội tuyển bóng chuyền nữ Việt Nam đã xuất sắc giành chức vô địch tại SEA Games 2026.
                  Với sự chỉ đạo của huấn luyện viên Park Hang-seo, các vận động viên đã thể hiện phong độ
                  xuất sắc trong toàn bộ giải đấu. Đặc biệt là các ngôi sao như Lê Thanh Thúy, Bùi Thị Tuyết Trinh
                  đã có những màn trình diễn ấn tượng. Chiến thắng này là niềm tự hào lớn cho thể thao Việt Nam.`
            },
            2: {
                title: "Trí tuệ nhân tạo sẽ thay đổi cuộc sống con người",
                content: `Trí tuệ nhân tạo (AI) đang dần trở thành part không thể thiếu trong đời sống.
                  Với sự phát triển của machine learning, deep learning, các ứng dụng AI ngày càng thông minh.
                  Python, TensorFlow, PyTorch là những công cụ được các developer sử dụng để xây dựng
                  các mô hình AI. Tương lai sắp tới, AI sẽ xuất hiện ở khắp mọi nơi.`
            },
            3: {
                title: "Kinh tế Việt Nam tăng trưởng 6,5% trong quý I",
                content: `Báo cáo của Tổng cục Thống kê cho biết, GDP Việt Nam tăng trưởng 6,5% trong quý I năm 2026.
                  Đây là con số vô cùng ấn tượng so với cùng kỳ năm ngoái. Các lĩnh vực công nghiệp, dịch vụ
                  và nông nghiệp đều có đóng góp tích cực. Đặc biệt, lĩnh vực xuất khẩu có những tín hiệu khả quan
                  khi số đơn đặt hàng mới tăng lên đáng kể. Các chuyên gia kinh tế lạc quan về triển vọng của nền kinh tế.`
            },
            4: {
                title: "Sức khỏe tinh thần trở thành vấn đề cấp thiết",
                content: `Theo các chuyên gia sức khỏe, tỷ lệ mắc bệnh trầm cảm và lo âu ở giới trẻ đang tăng lên đáng kể.
                  Áp lực từ công việc, học tập, cũng như tác động của mạng xã hội là những nguyên nhân chính.
                  Các bác sĩ tâm lý khuyến cáo cần tăng cường nhận thức về sức khỏe tinh thần.
                  Xây dựng các hỗ trợ cộng đồng để giúp những người gặp khó khăn là rất cần thiết.`
            }
        };

        function loadExample(id) {
            const example = examples[id];
            document.getElementById('title').value = example.title;
            document.getElementById('content').value = example.content;
        }

        // Form submit
        document.getElementById('aiTestForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const title = document.getElementById('title').value;
            const content = document.getElementById('content').value;

            const resultContent = document.getElementById('resultContent');
            resultContent.innerHTML =
                '<div class="text-center"><div class="spinner-border"></div><p class="mt-2">Đang xử lý...</p></div>';

            try {
                const response = await fetch('{{ route('admin.test-ai.process') }}', {
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

                if (data.success) {
                    displayResult(data.data);
                } else {
                    resultContent.innerHTML = `<div class="alert alert-danger">Lỗi: ${data.message}</div>`;
                }
            } catch (error) {
                resultContent.innerHTML = `<div class="alert alert-danger">Lỗi kết nối: ${error.message}</div>`;
            }
        });

        function displayResult(data) {
            const resultContent = document.getElementById('resultContent');

            let tagsHtml = '';
            if (data.tags && data.tags.length > 0) {
                tagsHtml = data.tags.map(tag => `<span class="badge bg-info me-2 mb-2">${tag}</span>`).join('');
            } else {
                tagsHtml = '<span class="text-muted">Không có tags</span>';
            }

            let html = `
        <div class="result-item mb-4">
            <h6 class="text-muted text-uppercase">Danh mục dự đoán</h6>
            <div class="alert alert-info mb-0">
                <h5 class="mb-0">${data.category_name}</h5>
            </div>
        </div>

        <div class="result-item mb-4">
            <h6 class="text-muted text-uppercase">Độ tin cây</h6>
            <div class="progress" style="height: 25px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: ${parseFloat(data.confidence_score)}%;">
                    ${data.confidence_score}
                </div>
            </div>
        </div>

        <div class="result-item mb-4">
            <h6 class="text-muted text-uppercase">Tóm tắt</h6>
            <p class="mb-0">${data.summary}</p>
        </div>

        <div class="result-item mb-4">
            <h6 class="text-muted text-uppercase">Tags/Từ khóa</h6>
            <div>${tagsHtml}</div>
        </div>

        <div class="result-item">
            <h6 class="text-muted text-uppercase">Metadata</h6>
            <div class="bg-light p-3 rounded">
                <small>
                    <strong>Sentiment:</strong> ${data.metadata.sentiment || 'N/A'}<br>
                    <strong>AI Provider:</strong> ${data.metadata.ai_provider || 'N/A'}
                </small>
            </div>
        </div>
    `;

            resultContent.innerHTML = html;
        }
    </script>

    <style>
        .result-item {
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 1rem;
        }

        .result-item:last-child {
            border-bottom: none;
        }
    </style>
@endsection
