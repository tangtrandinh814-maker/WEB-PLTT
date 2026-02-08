<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$request = \Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

$article = \App\Models\Article::create([
    'title' => 'Trao quà Tết Bính Ngọ 2026 tại Đà Nẵng: Gắn kết Đà Nẵng, lan tỏa yêu thương',
    'slug' => 'trao-qua-tet-binh-ngo-da-nang-2026',
    'summary' => 'Ngày 8.2, tại TP.Đà Nẵng, các cơ quan, đơn vị cùng tổ chức chương trình trao 1.300 suất quà Tết cho đồng bào khó khăn tại các xã biên giới, công nhân khu công nghiệp với tổng giá trị 1,5 tỉ đồng.',
    'content' => 'Ngày 8.2, tại TP.Đà Nẵng, Cơ quan thường trú khu vực miền Trung - Đài Tiếng nói Việt Nam (VOV miền Trung), Bộ Tư lệnh Quân khu 5, Ủy ban MTTQ Việt Nam TP.Đà Nẵng, Sở Y tế TP.Đà Nẵng cùng nhiều cơ quan, đơn vị và doanh nghiệp đồng hành tổ chức chương trình trao quà Tết Bính Ngọ 2026 với chủ đề "Gắn kết Đà Nẵng, lan tỏa yêu thương".

Tại chương trình, ban tổ chức đã trao 1.300 suất quà (mỗi suất trị giá 600.000 đồng) tặng đồng bào khó khăn tại các xã biên giới Hùng Sơn, A Vương và công nhân làm việc tại khu công nghệ cao, các khu công nghiệp ở Đà Nẵng.

Đồng thời, các y bác sĩ đã khám bệnh chuyên khoa, cấp thuốc miễn phí cho 600 người dân nghèo xã A Vương, 500 công nhân có hoàn cảnh khó khăn và khám sàng lọc tim mạch cho 500 trẻ em mầm non, tiểu học tại Trường tiểu học Ba Lế (xã A Vương).

Tổng giá trị quà tết và các hoạt động an sinh trong chương trình khoảng 1,5 tỉ đồng.

Tại chương trình, Ủy ban MTTQ Việt Nam TP.Đà Nẵng hỗ trợ 270 triệu đồng tặng quà cho người nghèo tại xã Hùng Sơn và A Vương; Thường trực HĐND TP.Đà Nẵng trao quà cho các xã A Vương, Tây Giang và Hùng Sơn; Đảng ủy phường Hải Châu trao 100 suất quà (mỗi suất trị giá 1 triệu đồng) cho đồng bào xã Hùng Sơn.

Dịp này, các bệnh viện tuyến cuối tại TP.Đà Nẵng đã cử đội ngũ y bác sĩ giàu kinh nghiệm, mang theo nhiều trang thiết bị y tế như máy siêu âm, điện tim, máy chụp X-quang lưu động để phục vụ công tác khám chữa bệnh.

Bộ Tư lệnh Quân khu 5 và các bệnh viện tham gia chương trình hỗ trợ hơn 1.000 cơ số thuốc, 300 cặp kính với tổng trị giá trên 260 triệu đồng để cấp phát cho người dân. Bên cạnh đó, 700 hộ nghèo tại 2 xã biên giới Hùng Sơn và A Vương cũng được hỗ trợ 4,5 tấn gạo.',
    'category_id' => 1,
    'source_id' => 1,
    'author' => 'Tơ Châu',
    'image_url' => 'https://images.unsplash.com/photo-1559027615-cd2628902d4a?w=800&h=400&fit=crop',
    'original_url' => 'https://vov.vn',
    'ai_metadata' => json_encode(['sentiment' => 'positive', 'ai_provider' => 'gemini']),
    'published_at' => now(),
    'is_published' => true,
    'is_featured' => true,
    'views_count' => 856,
]);

echo "✅ Đã thêm bài viết: " . $article->title . "\n";
echo "📄 Slug: " . $article->slug . "\n";
