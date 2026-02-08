<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleView;
use App\Models\Category;
use App\Models\Source;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@news.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create categories
        $categories = [
            ['name' => 'Thời Sự', 'color' => '#ef4444', 'icon' => '📰', 'order' => 1],
            ['name' => 'Thế Giới', 'color' => '#3b82f6', 'icon' => '🌍', 'order' => 2],
            ['name' => 'Kinh Doanh', 'color' => '#10b981', 'icon' => '💼', 'order' => 3],
            ['name' => 'Công Nghệ', 'color' => '#8b5cf6', 'icon' => '💻', 'order' => 4],
            ['name' => 'Giải Trí', 'color' => '#ec4899', 'icon' => '🎬', 'order' => 5],
            ['name' => 'Thể Thao', 'color' => '#f59e0b', 'icon' => '⚽', 'order' => 6],
            ['name' => 'Sức Khỏe', 'color' => '#06b6d4', 'icon' => '🏥', 'order' => 7],
            ['name' => 'Giáo Dục', 'color' => '#84cc16', 'icon' => '📚', 'order' => 8],
            ['name' => 'Pháp Luật', 'color' => '#6366f1', 'icon' => '⚖️', 'order' => 9],
            ['name' => 'Đời Sống', 'color' => '#14b8a6', 'icon' => '🏡', 'order' => 10],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create news sources
        $sources = [
            [
                'name' => 'VnExpress',
                'url' => 'https://vnexpress.net',
                'rss_url' => 'https://vnexpress.net/rss/tin-moi-nhat.rss',
            ],
            [
                'name' => 'Tuổi Trẻ',
                'url' => 'https://tuoitre.vn',
                'rss_url' => 'https://tuoitre.vn/rss/tin-moi-nhat.rss',
            ],
            [
                'name' => 'Thanh Niên',
                'url' => 'https://thanhnien.vn',
                'rss_url' => 'https://thanhnien.vn/rss/home.rss',
            ],
            [
                'name' => 'Zing News',
                'url' => 'https://zingnews.vn',
                'rss_url' => 'https://zingnews.vn/rss/tin-moi.rss',
            ],
            [
                'name' => 'Dân Trí',
                'url' => 'https://dantri.com.vn',
                'rss_url' => 'https://dantri.com.vn/rss.rss',
            ],
        ];

        foreach ($sources as $source) {
            Source::create($source);
        }

        // Create test articles
        $articles_data = [
            [
                'title' => 'AI sẽ thay đổi cuộc sống con người trong 5 năm tới',
                'category_id' => 4,
                'source_id' => 1,
                'summary' => 'Các chuyên gia công nghệ dự đoán rằng trí tuệ nhân tạo sẽ trở thành phần không thể thiếu trong mọi khía cạnh cuộc sống...',
                'content' => 'Trí tuệ nhân tạo (AI) đã trở thành một trong những công nghệ quan trọng nhất của thời đại. Các nhà khoa học và chuyên gia công nghệ đang dự đoán rằng trong 5 năm tới, AI sẽ thay đổi hoàn toàn cách con người sống và làm việc.

Từ y tế, giáo dục, đến các ngành công nghiệp truyền thống, AI đang dần dần xâm nhập và tạo ra những thay đổi tích cực. Các ứng dụng của AI đã giúp cải thiện hiệu suất làm việc, giảm chi phí và tạo ra những sản phẩm mới.

Tuy nhiên, cùng với những lợi ích đó, AI cũng đặt ra những thách thức về đạo đức, bảo mật và việc làm. Các chính phủ trên thế giới đang nỗ lực để tạo ra những quy định phù hợp để kiểm soát sự phát triển của AI.',
                'author' => 'Nguyễn Văn A',
                'is_featured' => true,
                'is_published' => true,
                'views_count' => 1250,
                'ai_confidence_score' => 0.92,
                'image_url' => 'https://images.unsplash.com/photo-1677442d019cecf0f2e6c393a0b07f15?w=800&h=400&fit=crop',
            ],
            [
                'title' => 'Việt Nam giành huy chương vàng tại SEA Games 2026',
                'category_id' => 6,
                'source_id' => 2,
                'summary' => 'Đội thể thao Việt Nam đã có một ngày thành công tại SEA Games 2026 với nhiều huy chương vàng...',
                'content' => 'Đội tuyển thể thao Việt Nam tiếp tục ghi dấu ấn tại SEA Games 2026 với những thành tích xuất sắc. Các vận động viên Việt Nam đã giành được 5 huy chương vàng trong các môn thể thao khác nhau.

Đặc biệt, trong bộ môn bơi lội, Việt Nam đã có những thành tích ấn tượng với các kỷ lục quốc gia bị phá. Trong các môn võ thuật, Việt Nam cũng thể hiện sức mạnh của mình với nhiều chiến thắng áp đảo.

Bên cạnh đó, các vận động viên Việt Nam cũng đạt được thành tích tốt trong các môn thể thao truyền thống như cầu lông, bóng chuyền nữ.',
                'author' => 'Trần Thị B',
                'is_featured' => true,
                'is_published' => true,
                'views_count' => 2100,
                'ai_confidence_score' => 0.88,
                'image_url' => 'https://images.unsplash.com/photo-1517836357463-d25ddfcbf042?w=800&h=400&fit=crop',
            ],
            [
                'title' => 'Xu hướng sức khỏe tinh thần trong giới trẻ',
                'category_id' => 7,
                'source_id' => 3,
                'summary' => 'Các chuyên gia sức khỏe cảnh báo về tình trạng ứ đọng về sức khỏe tinh thần ở giới trẻ...',
                'content' => 'Sức khỏe tinh thần trở thành vấn đề quan trọng đối với giới trẻ hiện nay. Theo khảo sát gần đây, tỷ lệ thanh niên gặp vấn đề về trầm cảm và lo âu tăng lên đáng kể.

Các nguyên nhân chính bao gồm áp lực học tập, công việc, và tác động của mạng xã hội. Nhiều thanh niên cảm thấy bị cô lập và lo lắng về tương lai của họ.

Các chuyên gia khuyên rằng cần tăng cường nhận thức về sức khỏe tinh thần, đồng thời xây dựng các hỗ trợ cộng đồng để giúp những người gặp khó khăn.',
                'author' => 'Phạm Văn C',
                'is_featured' => false,
                'is_published' => true,
                'views_count' => 850,
                'ai_confidence_score' => 0.85,
                'image_url' => 'https://images.unsplash.com/photo-1576091160550-112173faf246?w=800&h=400&fit=crop',
            ],
            [
                'title' => 'Kinh tế Việt Nam tăng trưởng 6,5% trong quý I',
                'category_id' => 3,
                'source_id' => 1,
                'summary' => 'Theo báo cáo của Tổng cục Thống kê, GDP Việt Nam tăng trưởng ấn tượng trong quý đầu năm...',
                'content' => 'Nền kinh tế Việt Nam tiếp tục thể hiện những dấu hiệu tích cực trong quý I năm 2026 với mức tăng trưởng GDP đạt 6,5%. Đây là con số ấn tượng so với cùng kỳ năm ngoái.

Các lĩnh vực công nghiệp, dịch vụ và nông nghiệp đều có đóng góp tích cực vào sự tăng trưởng này. Đặc biệt, lĩnh vực xuất khẩu có những tín hiệu khả quan khi số đơn đặt hàng mới tăng lên.

Tuy nhiên, các chuyên gia kinh tế cũng chỉ ra những thách thức như lạm phát, biến động tỷ giá mà Việt Nam cần đối mặt.',
                'author' => 'Lê Văn D',
                'is_featured' => true,
                'is_published' => true,
                'views_count' => 1650,
                'ai_confidence_score' => 0.90,
                'image_url' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&h=400&fit=crop',
            ],
            [
                'title' => 'Phim mới của đạo diễn nổi tiếng khiến khán giả thót tim',
                'category_id' => 5,
                'source_id' => 4,
                'summary' => 'Bộ phim mới vừa công chiếu thu hút lượng khán giả lớn với những cảnh quay ấn tượng...',
                'content' => 'Phim trình chiếu mới của đạo diễn nổi tiếng đã tạo nên sức hút lớn tại các rạp chiếu phim. Bộ phim kết hợp giữa kỹ thuật quay phim hiện đại và câu chuyện hấp dẫn.

Các diễn viên chính trong phim đã có những màn trình diễn xuất sắc, khiến khán giả vỡ òa. Phim đang trên đường trở thành một trong những bộ phim ăn khách nhất năm nay.

Theo các nhận xét của giới phê bình, đây là một tác phẩm điện ảnh đáng xem và góp phần nâng cao chất lượng của điện ảnh Việt Nam.',
                'author' => 'Hoàng Thị E',
                'is_featured' => false,
                'is_published' => true,
                'views_count' => 3200,
                'ai_confidence_score' => 0.87,
                'image_url' => 'https://images.unsplash.com/photo-1516606668158-b5b0f6f8a9ec?w=800&h=400&fit=crop',
            ],
            [
                'title' => 'Chương trình giáo dục mới giúp học sinh phát triển toàn diện',
                'category_id' => 8,
                'source_id' => 2,
                'summary' => 'Các trường học đang triển khai chương trình giáo dục mới nhằm giúp học sinh phát triển kỹ năng...',
                'content' => 'Bộ GD&ĐT vừa công bố chương trình giáo dục mới với những thay đổi lớn trong cách dạy học. Chương trình này tập trung vào phát triển kỹ năng thực hành, tư duy sáng tạo và khả năng làm việc nhóm.

Các trường học trên cả nước đang dần dần áp dụng chương trình mới này. Giáo viên cũng được tập huấn để hiểu rõ hơn về những nội dung và cách thức dạy học mới.

Theo đánh giá ban đầu, chương trình mới này đã nhận được phản ứng tích cực từ học sinh, phụ huynh và giáo viên.',
                'author' => 'Ngô Văn F',
                'is_featured' => false,
                'is_published' => true,
                'views_count' => 920,
                'ai_confidence_score' => 0.86,
                'image_url' => 'https://images.unsplash.com/photo-1427504494785-cda0e4ddb604?w=800&h=400&fit=crop',
            ],
            [
                'title' => 'Tin tức pháp luật: Luật mới về bảo vệ môi trường',
                'category_id' => 9,
                'source_id' => 5,
                'summary' => 'Quốc hội vừa thông qua luật mới nhằm tăng cường bảo vệ môi trường và phạt nguồn gây ô nhiễm...',
                'content' => 'Quốc hội Việt Nam đã thông qua Luật Bảo vệ Môi trường sửa đổi với những quy định mới và hình phạt nặng hơn đối với các vi phạm. Luật này sẽ có hiệu lực từ ngày 1 tháng 6 năm 2026.

Những điểm chính của luật mới bao gồm: tăng hạn mức phạt tiền đối với các hành vi gây ô nhiễm, bổ sung quy định về bảo vệ sinh thái biển, và nâng cao tiêu chuẩn môi trường trong các hoạt động công nghiệp.

Các chuyên gia môi trường đánh giá cao những nỗ lực này của Nhà nước trong việc bảo vệ môi trường cho các thế hệ tương lai.',
                'author' => 'Đặng Văn G',
                'is_featured' => false,
                'is_published' => true,
                'views_count' => 640,
                'ai_confidence_score' => 0.83,
                'image_url' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=800&h=400&fit=crop',
            ],
            [
                'title' => 'Xu hướng nội thất hiện đại cho nhà ở thao gọn',
                'category_id' => 10,
                'source_id' => 3,
                'summary' => 'Những ý tưởng thiết kế nội thất hiện đại đang được ưa chuộng bởi những gia đình sống trong nhà nhỏ...',
                'content' => 'Sống trong không gian nhỏ đòi hỏi sự sáng tạo trong việc sắp xếp nội thất. Những xu hướng thiết kế hiện đại nhấn mạnh vào tính thực dụng và thẩm mỹ.

Các ý tưởng như sử dụng đồ nội thất đa năng, đơn giản hóa các vật dụng, và tận dụng tối đa không gian dọc là những giải pháp được ưa chuộng.

Với những mẫu thiết kế này, các gia đình có thể tạo ra một không gian sống thoải mái, đẹp mắt và hiệu quả.',
                'author' => 'Đinh Thị H',
                'is_featured' => false,
                'is_published' => true,
                'views_count' => 780,
                'ai_confidence_score' => 0.84,
                'image_url' => 'https://images.unsplash.com/photo-1594736461245-aa84e801d877?w=800&h=400&fit=crop',
            ],
            [
                'title' => 'Tin thế giới: Hội nghị khí hậu diễn ra tại Thụy Sĩ',
                'category_id' => 2,
                'source_id' => 1,
                'summary' => 'Hội nghị khí hậu quốc tế đang diễn ra tại Zurich với sự tham gia của hơn 190 quốc gia...',
                'content' => 'Hội nghị khí hậu quốc tế lần thứ 26 (COP26) đang diễn ra tại Zurich, Thụy Sĩ với sự tham gia của các đại biểu từ hơn 190 quốc gia trên thế giới.

Mục tiêu chính của hội nghị là đạt được những thỏa thuận về giảm phát thải khí nhà kính và các biện pháp ứng phó với biến đổi khí hậu toàn cầu.

Các quốc gia đang thảo luận về các cam kết mới, nguồn tài chính cho các nước đang phát triển, và các biển pháp để giảm thiểu tác động của biến đổi khí hậu.',
                'author' => 'Bùi Văn I',
                'is_featured' => true,
                'is_published' => true,
                'views_count' => 1450,
                'ai_confidence_score' => 0.89,
                'image_url' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=800&h=400&fit=crop',
            ],
        ];

        $sourceUrls = [
            1 => 'https://vnexpress.net',
            2 => 'https://tuoitre.vn',
            3 => 'https://thanhnien.vn',
            4 => 'https://zingnews.vn',
            5 => 'https://dantri.com.vn',
        ];

        foreach ($articles_data as $article) {
            $sourceUrl = $sourceUrls[$article['source_id']] ?? 'https://vnexpress.net';
            Article::create(array_merge($article, [
                'slug' => Str::slug($article['title']),
                'original_url' => $sourceUrl . '/' . Str::slug($article['title']),
                'ai_metadata' => [
                    'sentiment' => ['positive', 'neutral', 'negative'][array_rand([0, 1, 2])],
                    'ai_provider' => 'gemini',
                ],
            ]));
        }

        // Create article views for some articles
        $articles = Article::all();
        foreach ($articles as $article) {
            $views_count = rand(5, 20);
            for ($i = 0; $i < $views_count; $i++) {
                ArticleView::create([
                    'article_id' => $article->id,
                    'ip_address' => fake()->ipv4(),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ]);
            }
        }

        $this->command->info('Database seeded successfully!');
    }
}
