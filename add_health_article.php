<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$request = \Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

$article = \App\Models\Article::create([
    'title' => 'Phụ huynh bức xúc: Nhà trường "ngó lơ" sức khỏe con em khi ăn bán trú',
    'slug' => 'phu-huynh-buc-xuc-truong-ngo-lo-suc-khoe-con',
    'summary' => 'Xuyên suốt buổi họp, nhiều phụ huynh bức xúc về việc tình trạng sức khỏe của con em bị nhà trường "ngó lơ", kể cả khi các bé có dấu hiệu ngộ độc thực phẩm khi ăn bán trú tại trường Nguyễn Văn Hưởng.',
    'content' => 'Xuyên suốt buổi họp, không ít phụ huynh bức xúc về việc tình trạng sức khỏe của con em mình bị nhà trường "ngó lơ", kể cả khi các bé có dấu hiệu ngộ độc thực phẩm khi ăn bán trú.

Đại diện UBND phường Phú Thuận tham dự buổi họp, bà Dương Thị Cẩm Hồng – Phó Chủ tịch UBND phường, đã có những ghi nhận và chỉ đạo nhà trường phải cung cấp đầy đủ hồ sơ hợp đồng, công văn giữa trường và công ty Sago Food cho toàn thể phụ huynh được nắm. Song song với đó là việc nhanh chóng gửi lại báo cáo toàn bộ sự việc về ngộ độc thực phẩm tại trường từ năm học trước cho tới thời điểm hiện tại cho Ủy ban phường trong thời gian sớm nhất.

Đồng thời, yêu cầu cô Nhiên, Hiệu trưởng của trường phải có thư xin lỗi gửi đến quý phụ huynh về những thiếu sót đã khiến phụ huynh bức xúc suốt thời gian vừa qua.

Thay vì tập trung làm việc, các phụ huynh có con học ở trường Nguyễn Văn Hưởng, phường Phú Thuận, TP.HCM phải bỏ dở công việc để đưa từng phần cơm cho con mình. Một số phụ huynh thậm chí phải đón con về dù đã đăng ký cho con ăn bán trú trước đó.',
    'category_id' => 7,
    'source_id' => 1,
    'author' => 'Phóng viên',
    'image_url' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800&h=400&fit=crop',
    'original_url' => 'https://thanhnien.vn',
    'ai_metadata' => json_encode(['sentiment' => 'negative', 'ai_provider' => 'gemini']),
    'published_at' => now(),
    'is_published' => true,
    'is_featured' => false,
    'views_count' => 1243,
]);

echo "✅ Đã thêm bài viết: " . $article->title . "\n";
echo "📄 Slug: " . $article->slug . "\n";
echo "📊 Danh mục: Sức Khỏe (ID: 7)\n";
