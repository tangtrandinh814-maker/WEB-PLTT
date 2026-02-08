<?php

use App\Models\Article;

$articles = [
    [
        'title' => 'Trí Tuệ Nhân Tạo Cách Mạng Hóa Công Nghệ',
        'slug' => 'tri-tue-nhan-tao-cach-mang-hoa-cong-nghe',
        'summary' => 'AI đang thay đổi cách chúng ta sống và làm việc',
        'content' => 'Trí tuệ nhân tạo (AI) đang trở thành một phần thiết yếu của cuộc sống hiện đại...',
        'category_id' => 1,
        'source_id' => 1,
        'author' => 'Admin',
        'image_url' => 'https://images.unsplash.com/photo-1677442d019cecf0f2e6c393a0b07f15?w=800',
        'published_at' => now(),
        'is_published' => true,
        'is_featured' => true,
        'views_count' => 1205,
    ],
    [
        'title' => 'Blockchain: Công Nghệ Của Tương Lai',
        'slug' => 'blockchain-cong-nghe-cua-tuong-lai',
        'summary' => 'Khám phá ứng dụng của blockchain trong tài chính',
        'content' => 'Blockchain là công nghệ sổ cái phân tán được sử dụng...',
        'category_id' => 2,
        'source_id' => 1,
        'author' => 'Admin',
        'image_url' => 'https://images.unsplash.com/photo-1639762681033-6461b3b0edd9?w=800',
        'published_at' => now(),
        'is_published' => true,
        'is_featured' => true,
        'views_count' => 892,
    ],
    [
        'title' => '5G Sẽ Thay Đổi Kết Nối Internet',
        'slug' => '5g-se-thay-doi-ket-noi-internet',
        'summary' => 'Tốc độ, độ trễ thấp và khả năng kết nối',
        'content' => '5G là thế hệ thứ năm của công nghệ di động...',
        'category_id' => 3,
        'source_id' => 2,
        'author' => 'Admin',
        'image_url' => 'https://images.unsplash.com/photo-1637745212787-267f7f80f1ff?w=800',
        'published_at' => now(),
        'is_published' => true,
        'views_count' => 654,
    ],
    [
        'title' => 'Máy Tính Lượng Tử: Tương Lai Của Tính Toán',
        'slug' => 'may-tinh-luong-tu-tuong-lai-tinh-toan',
        'summary' => 'Máy tính lượng tử có thể giải quyết những vấn đề phức tạp',
        'content' => 'Máy tính lượng tử sử dụng các nguyên lý của cơ học lượng tử...',
        'category_id' => 1,
        'source_id' => 1,
        'author' => 'Admin',
        'image_url' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=800',
        'published_at' => now(),
        'is_published' => true,
        'views_count' => 1520,
    ],
    [
        'title' => 'IoT Trong Nhà Thông Minh',
        'slug' => 'iot-trong-nha-thong-minh',
        'summary' => 'Cách Internet of Things tạo ra những ngôi nhà thông minh',
        'content' => 'Internet of Things (IoT) cho phép các thiết bị kết nối...',
        'category_id' => 2,
        'source_id' => 2,
        'author' => 'Admin',
        'image_url' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800',
        'published_at' => now(),
        'is_published' => true,
        'views_count' => 743,
    ],
];

foreach ($articles as $data) {
    Article::create($data);
}

echo "✅ Created " . count($articles) . " sample articles with images!";
