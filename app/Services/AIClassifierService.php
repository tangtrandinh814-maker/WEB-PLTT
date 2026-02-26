<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AIClassifierService
{
    protected string $apiKey;
    protected string $apiUrl;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model', 'gemini-2.0-flash');
        $baseUrl = config('services.gemini.api_url', 'https://generativelanguage.googleapis.com/v1beta');
        $this->apiUrl = rtrim($baseUrl, '/') . '/models/' . $this->model . ':generateContent';
    }

    /**
     * Classify article content using Gemini AI
     */
    public function classifyArticle(string $title, string $content): array
    {
        $categories = Category::active()->pluck('name')->toArray();

        $prompt = $this->buildPrompt($title, $content, $categories);

        try {
            $response = Http::timeout(30)->post($this->apiUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.3,
                    'maxOutputTokens' => 500,
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
                return $this->parseAIResponse($text, $categories, $title, $content);
            } else {
                Log::error('Gemini API Error: ' . $response->body());
                return $this->fallbackClassification($title, $content);
            }
        } catch (\Exception $e) {
            Log::error('AI Classification Error: ' . $e->getMessage());
            return $this->fallbackClassification($title, $content);
        }
    }

    /**
     * Build prompt for AI
     */
    private function buildPrompt(string $title, string $content, array $categories): string
    {
        $categoriesList = implode(', ', $categories);
        $contentPreview = Str::limit(strip_tags($content), 1500);

        return <<<PROMPT
Bạn là hệ thống phân loại bài báo tiếng Việt. Hãy phân tích và chọn ĐÚNG MỘT danh mục phù hợp nhất.

Tiêu đề: {$title}

Nội dung: {$contentPreview}

DANH SÁCH DANH MỤC (chọn CHÍNH XÁC một trong các tên sau):
{$categoriesList}

HƯỚNG DẪN PHÂN LOẠI:
- "Thời Sự": Tin tức trong nước, chính trị Việt Nam, chính phủ, quốc hội, chính sách, sự kiện nội bộ
- "Thế Giới": Tin tức quốc tế, quan hệ ngoại giao, sự kiện ở nước ngoài, lãnh đạo nước ngoài
- "Kinh Doanh": Kinh tế, tài chính, chứng khoán, bất động sản, doanh nghiệp, thương mại
- "Công Nghệ": Công nghệ thông tin, điện tử, phần mềm, AI/trí tuệ nhân tạo, Internet, thiết bị số
- "Giải Trí": Showbiz, phim ảnh, âm nhạc, nghệ sĩ, ca sĩ, diễn viên, truyền hình, scandal giới giải trí
- "Thể Thao": Bóng đá, bóng rổ, tennis, vận động viên, giải đấu thể thao, huấn luyện viên
- "Sức Khỏe": Y tế, bệnh viện, bác sĩ, thuốc, dịch bệnh, chăm sóc sức khỏe, dinh dưỡng
- "Giáo Dục": Trường học, đại học, thi cử, sinh viên, giáo viên, học bổng, tuyển sinh
- "Pháp Luật": Tội phạm, công an, bắt giữ, xét xử, tòa án, vụ án, trộm cắp, lừa đảo, tai nạn giao thông
- "Đời Sống": Cuộc sống, gia đình, tình yêu, mẹo vặt, du lịch, ẩm thực, thời trang, nhà cửa

LƯU Ý QUAN TRỌNG:
- Ưu tiên phân tích TIÊU ĐỀ để xác định chủ đề chính
- Trường "category" phải CHÍNH XÁC một trong các tên danh mục ở trên (bao gồm dấu tiếng Việt)
- Nếu bài viết về nghệ sĩ, diễn viên, ca sĩ → "Giải Trí"
- Nếu bài viết về vụ án, công an, bắt giữ → "Pháp Luật"
- Nếu bài viết về lãnh đạo/chính trị nước ngoài → "Thế Giới"

Trả về JSON (chỉ JSON thuần, KHÔNG có markdown):
{
    "category": "tên danh mục chính xác",
    "confidence": 0.95,
    "summary": "tóm tắt ngắn gọn 2-3 câu",
    "tags": ["tag1", "tag2", "tag3"],
    "sentiment": "positive/neutral/negative"
}
PROMPT;
    }

    /**
     * Parse AI response
     */
    private function parseAIResponse(string $response, array $categories, string $title = '', string $content = ''): array
    {
        // Remove markdown code blocks if present
        $response = preg_replace('/```json\s*|\s*```/', '', $response);
        $response = trim($response);

        try {
            $data = json_decode($response, true);

            if (!$data) {
                throw new \Exception('Invalid JSON response');
            }

            // Find matching category with multiple strategies
            $categoryName = trim($data['category'] ?? '');
            $category = $this->matchCategory($categoryName);

            // If AI failed to match, try fallback
            if (!$category && $title && $content) {
                Log::warning("AI category '{$categoryName}' not matched, using fallback");
                return $this->fallbackClassification($title, $content);
            }

            return [
                'category_id' => $category?->id,
                'confidence_score' => $data['confidence'] ?? 0.5,
                'summary' => $data['summary'] ?? null,
                'tags' => $data['tags'] ?? [],
                'metadata' => [
                    'sentiment' => $data['sentiment'] ?? 'neutral',
                    'ai_category_suggestion' => $categoryName,
                    'ai_provider' => 'gemini'
                ]
            ];
        } catch (\Exception $e) {
            Log::error('AI Response Parse Error: ' . $e->getMessage());
            if ($title && $content) {
                return $this->fallbackClassification($title, $content);
            }
            return [];
        }
    }

    /**
     * Match AI-returned category name to database category
     */
    private function matchCategory(string $categoryName): ?Category
    {
        if (empty($categoryName)) {
            return null;
        }

        // Strategy 1: Exact match
        $category = Category::where('name', $categoryName)->first();
        if ($category) return $category;

        // Strategy 2: Case-insensitive match
        $category = Category::whereRaw('LOWER(name) = ?', [mb_strtolower($categoryName)])->first();
        if ($category) return $category;

        // Strategy 3: Normalize Vietnamese diacritics and match
        $normalizedInput = $this->normalizeVietnamese($categoryName);
        $allCategories = Category::active()->get();
        foreach ($allCategories as $cat) {
            $normalizedCat = $this->normalizeVietnamese($cat->name);
            if ($normalizedInput === $normalizedCat) {
                return $cat;
            }
        }

        // Strategy 4: Partial match (category name contains input or vice versa)
        foreach ($allCategories as $cat) {
            if (
                Str::contains(mb_strtolower($cat->name), mb_strtolower($categoryName)) ||
                Str::contains(mb_strtolower($categoryName), mb_strtolower($cat->name))
            ) {
                return $cat;
            }
        }

        // Strategy 5: Slug-based match
        $inputSlug = Str::slug($categoryName);
        foreach ($allCategories as $cat) {
            if (Str::slug($cat->name) === $inputSlug) {
                return $cat;
            }
        }

        return null;
    }

    /**
     * Normalize Vietnamese text by removing diacritics
     */
    private function normalizeVietnamese(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $search =  [
            'à',
            'á',
            'ạ',
            'ả',
            'ã',
            'â',
            'ầ',
            'ấ',
            'ậ',
            'ẩ',
            'ẫ',
            'ă',
            'ằ',
            'ắ',
            'ặ',
            'ẳ',
            'ẵ',
            'è',
            'é',
            'ẹ',
            'ẻ',
            'ẽ',
            'ê',
            'ề',
            'ế',
            'ệ',
            'ể',
            'ễ',
            'ì',
            'í',
            'ị',
            'ỉ',
            'ĩ',
            'ò',
            'ó',
            'ọ',
            'ỏ',
            'õ',
            'ô',
            'ồ',
            'ố',
            'ộ',
            'ổ',
            'ỗ',
            'ơ',
            'ờ',
            'ớ',
            'ợ',
            'ở',
            'ỡ',
            'ù',
            'ú',
            'ụ',
            'ủ',
            'ũ',
            'ư',
            'ừ',
            'ứ',
            'ự',
            'ử',
            'ữ',
            'ỳ',
            'ý',
            'ỵ',
            'ỷ',
            'ỹ',
            'đ'
        ];
        $replace = [
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'i',
            'i',
            'i',
            'i',
            'i',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'y',
            'y',
            'y',
            'y',
            'y',
            'd'
        ];
        return str_replace($search, $replace, $text);
    }

    /**
     * Fallback classification when AI fails
     * Uses comprehensive keyword matching with title weighting
     */
    private function fallbackClassification(string $title, string $content): array
    {
        $titleLower = mb_strtolower($title);
        $contentLower = mb_strtolower(Str::limit(strip_tags($content), 3000));

        // Comprehensive keyword dictionary for all 10 categories
        // Keywords are ordered by specificity (more specific first)
        $keywords = [
            'Pháp Luật' => [
                'công an',
                'bắt giữ',
                'bắt giam',
                'khởi tố',
                'truy tố',
                'xét xử',
                'tòa án',
                'vụ án',
                'tội phạm',
                'lừa đảo',
                'trộm cắp',
                'cướp giật',
                'giết người',
                'mưu sát',
                'phạm tội',
                'hình sự',
                'tạm giam',
                'bị cáo',
                'bị can',
                'viện kiểm sát',
                'tai nạn giao thông',
                'tngt',
                'va chạm',
                'tử vong do tai nạn',
                'ma túy',
                'buôn lậu',
                'tham nhũng',
                'hối lộ',
                'đánh bạc',
                'pháp luật',
                'hình phạt',
                'án tù',
                'tử hình',
                'chung thân',
                'điều tra',
                'trinh sát',
                'cảnh sát',
                'csgt',
                'pccc',
                'thi hành án',
                'truy nã',
                'đâm chém',
                'bạo lực',
                'hiếp dâm',
            ],
            'Giải Trí' => [
                'nghệ sĩ',
                'diễn viên',
                'ca sĩ',
                'showbiz',
                'giải trí',
                'điện ảnh',
                'phim ảnh',
                'bộ phim',
                'đạo diễn',
                'kịch bản',
                'mv ',
                'music video',
                'album',
                'single',
                'concert',
                'hoa hậu',
                'người mẫu',
                'siêu mẫu',
                'á hậu',
                'truyền hình',
                'gameshow',
                'game show',
                'reality show',
                'talk show',
                'giải thưởng',
                'oscar',
                'grammy',
                'mtv',
                'scandal',
                'tin đồn',
                'hẹn hò',
                'chia tay',
                'đám cưới sao',
                'vpop',
                'kpop',
                'hollywood',
                'bollywood',
                'tiktok',
                'youtube',
                'influencer',
                'hot girl',
                'hot boy',
                'rapper',
                'idol',
                'celeb',
                'sao việt',
                'sao hàn',
                'nhạc sĩ',
                'biên đạo',
                'mc ',
                'người dẫn chương trình',
                'tiết lộ cuộc sống',
                'đời tư',
                'chuyện tình',
            ],
            'Thế Giới' => [
                'quốc tế',
                'thế giới',
                'nước ngoài',
                'toàn cầu',
                'mỹ ',
                'hoa kỳ',
                'trung quốc',
                'bắc kinh',
                'nga ',
                'moscow',
                'nhật bản',
                'tokyo',
                'hàn quốc',
                'seoul',
                'triều tiên',
                'ukraine',
                'nato',
                'liên hợp quốc',
                'liên hiệp quốc',
                'ngoại giao',
                'đại sứ',
                'lãnh sự',
                'summit',
                'hội nghị thượng đỉnh',
                'tổng thống',
                'thủ tướng nước',
                'cựu thủ tướng',
                'nữ hoàng',
                'hoàng gia',
                'trump',
                'biden',
                'putin',
                'tập cận bình',
                'xi jinping',
                'eu ',
                'châu âu',
                'châu á',
                'châu phi',
                'trung đông',
                'chiến tranh',
                'xung đột',
                'quân sự',
                'tên lửa',
                'hạt nhân',
                'g7',
                'g20',
                'asean',
                'apec',
                'brics',
                'canada',
                'úc',
                'australia',
                'anh quốc',
                'london',
                'paris',
                'pháp',
                'đức',
                'berlin',
                'ý ',
                'italy',
                'ấn độ',
                'india',
                'israel',
                'palestine',
                'gaza',
                'iran',
                'iraq',
                'syria',
                'thượng đỉnh',
                'hiệp ước',
                'hiệp định',
                'cấm vận',
                'trừng phạt',
            ],
            'Kinh Doanh' => [
                'kinh doanh',
                'doanh nghiệp',
                'doanh thu',
                'lợi nhuận',
                'doanh số',
                'kinh tế',
                'tài chính',
                'ngân hàng',
                'lãi suất',
                'tín dụng',
                'chứng khoán',
                'cổ phiếu',
                'vn-index',
                'vnindex',
                'thị trường',
                'bất động sản',
                'nhà đất',
                'chung cư',
                'dự án bds',
                'xuất khẩu',
                'nhập khẩu',
                'thương mại',
                'fdi',
                'đầu tư',
                'gdp',
                'lạm phát',
                'thuế',
                'ngân sách',
                'nợ công',
                'startup',
                'khởi nghiệp',
                'ipo',
                'mua bán sáp nhập',
                'm&a',
                'giá vàng',
                'giá dầu',
                'tỷ giá',
                'ngoại tệ',
                'đô la',
                'vingroup',
                'viettel',
                'fpt ',
                'masan',
                'vinamilk',
                'ngân hàng nhà nước',
                'bitcoin',
                'tiền ảo',
                'crypto',
            ],
            'Công Nghệ' => [
                'công nghệ',
                'technology',
                'tech',
                'trí tuệ nhân tạo',
                'artificial intelligence',
                'smartphone',
                'iphone',
                'samsung galaxy',
                'android',
                'ios',
                'laptop',
                'máy tính',
                'pc ',
                'phần mềm',
                'ứng dụng',
                'apple',
                'google',
                'microsoft',
                'meta',
                'amazon tech',
                'chatgpt',
                'openai',
                'machine learning',
                'deep learning',
                'robot ',
                'tự động hóa',
                'automation',
                'iot ',
                '5g ',
                'chip ',
                'bán dẫn',
                'semiconductor',
                'nvidia',
                'intel',
                'amd',
                'blockchain',
                'web3',
                'metaverse',
                'xe điện',
                'tesla',
                'pin lithium',
                'năng lượng tái tạo',
                'internet',
                'wifi',
                'bảo mật',
                'hacker',
                'an ninh mạng',
                'spacex',
                'vũ trụ',
                'nasa',
                'vệ tinh',
                'mã nguồn',
                'lập trình',
                'developer',
                'coding',
            ],
            'Thể Thao' => [
                'thể thao',
                'bóng đá',
                'bóng rổ',
                'bóng chuyền',
                'tennis',
                'olympic',
                'world cup',
                'sea games',
                'asiad',
                'vđv',
                'vận động viên',
                'hlv',
                'huấn luyện viên',
                'giải đấu',
                'chung kết',
                'bán kết',
                'tứ kết',
                'vòng bảng',
                'premier league',
                'la liga',
                'serie a',
                'bundesliga',
                'ligue 1',
                'champions league',
                'europa league',
                'v-league',
                'v.league',
                'manchester',
                'barcelona',
                'real madrid',
                'arsenal',
                'liverpool',
                'messi',
                'ronaldo',
                'mbappe',
                'haaland',
                'neymar',
                'đội tuyển',
                'tuyển việt nam',
                'park hang seo',
                'cầu thủ',
                'trọng tài',
                'bàn thắng',
                'hat-trick',
                'golf',
                'f1 ',
                'formula 1',
                'boxing',
                'mma',
                'ufc',
                'bơi lội',
                'điền kinh',
                'marathon',
                'cầu lông',
                'cờ vua',
            ],
            'Sức Khỏe' => [
                'sức khỏe',
                'y tế',
                'bệnh viện',
                'bác sĩ',
                'bệnh nhân',
                'thuốc',
                'dược phẩm',
                'vaccine',
                'vắc xin',
                'tiêm chủng',
                'covid',
                'corona',
                'đại dịch',
                'dịch bệnh',
                'lây nhiễm',
                'ung thư',
                'tiểu đường',
                'tim mạch',
                'huyết áp',
                'đột quỵ',
                'dinh dưỡng',
                'vitamin',
                'thực phẩm chức năng',
                'phẫu thuật',
                'ghép tạng',
                'cấp cứu',
                'hồi sức',
                'sức khỏe tâm thần',
                'trầm cảm',
                'stress',
                'lo âu',
                'bảo hiểm y tế',
                'bhyt',
                'viện phí',
                'who',
                'tổ chức y tế',
                'bộ y tế',
                'giảm cân',
                'tập thể dục',
                'yoga',
                'thiền',
            ],
            'Giáo Dục' => [
                'giáo dục',
                'đào tạo',
                'trường học',
                'đại học',
                'cao đẳng',
                'thi cử',
                'kỳ thi',
                'tốt nghiệp',
                'tuyển sinh',
                'xét tuyển',
                'sinh viên',
                'học sinh',
                'giáo viên',
                'thầy giáo',
                'cô giáo',
                'học bổng',
                'du học',
                '留学',
                'scholarship',
                'bộ giáo dục',
                'sở giáo dục',
                'bgdđt',
                'thpt',
                'thcs',
                'tiểu học',
                'mầm non',
                'điểm chuẩn',
                'điểm thi',
                'chương trình giáo dục',
                'luận văn',
                'nghiên cứu khoa học',
                'tiến sĩ',
                'thạc sĩ',
                'sách giáo khoa',
                'sgk',
                'giáo trình',
            ],
            'Thời Sự' => [
                'chính trị',
                'chính phủ',
                'quốc hội',
                'đảng',
                'ban chấp hành',
                'chính sách',
                'nghị quyết',
                'nghị định',
                'luật ',
                'pháp lệnh',
                'thủ tướng',
                'chủ tịch nước',
                'tổng bí thư',
                'phó thủ tướng',
                'bộ trưởng',
                'thứ trưởng',
                'ubnd',
                'hội đồng nhân dân',
                'tuyên truyền',
                'vận động',
                'cải cách',
                'đổi mới',
                'bầu cử',
                'đại hội',
                'hội nghị trung ương',
                'an ninh',
                'quốc phòng',
                'biên giới',
                'chủ quyền',
                'thiên tai',
                'bão ',
                'lũ lụt',
                'hạn hán',
                'động đất',
                'cứu hộ',
                'cứu nạn',
                'phòng chống',
                'dân sinh',
                'xã hội',
                'môi trường',
                'biến đổi khí hậu',
            ],
            'Đời Sống' => [
                'đời sống',
                'cuộc sống',
                'gia đình',
                'hôn nhân',
                'tình yêu',
                'mẹo vặt',
                'bí quyết',
                'kinh nghiệm sống',
                'du lịch',
                'phượt',
                'nghỉ dưỡng',
                'resort',
                'khách sạn',
                'ẩm thực',
                'món ăn',
                'nấu ăn',
                'nhà hàng',
                'quán ăn',
                'thời trang',
                'fashion',
                'mỹ phẩm',
                'làm đẹp',
                'skincare',
                'nhà cửa',
                'nội thất',
                'trang trí',
                'phong thủy',
                'nuôi dạy con',
                'mang thai',
                'bà bầu',
                'trẻ em',
                'thú cưng',
                'chó mèo',
                'cây cảnh',
                'tâm sự',
                'tình cảm',
                'mối quan hệ',
                'lối sống',
                'xu hướng',
                'trend',
            ],
        ];

        $maxScore = 0;
        $bestCategory = null;
        $scores = [];

        foreach ($keywords as $categoryName => $words) {
            $score = 0;
            foreach ($words as $word) {
                // Title matches count 3x more than content matches
                $titleMatches = mb_substr_count($titleLower, $word);
                $contentMatches = mb_substr_count($contentLower, $word);
                $score += ($titleMatches * 3) + $contentMatches;
            }

            $scores[$categoryName] = $score;

            if ($score > $maxScore) {
                $maxScore = $score;
                $bestCategory = $categoryName;
            }
        }

        // Log scores for debugging
        arsort($scores);
        Log::info("Fallback classification for: {$title}", [
            'top_scores' => array_slice($scores, 0, 5, true),
            'selected' => $bestCategory,
        ]);

        $category = $bestCategory ? Category::where('name', $bestCategory)->first() : null;

        if (!$category) {
            // Default to "Thời Sự" (general news) instead of first category
            $category = Category::where('name', 'Thời Sự')->first() ?? Category::first();
        }

        return [
            'category_id' => $category?->id,
            'confidence_score' => $maxScore > 0 ? min(0.7, $maxScore * 0.05) : 0.2,
            'summary' => Str::limit(strip_tags($content), 200),
            'tags' => [],
            'metadata' => [
                'sentiment' => 'neutral',
                'classification_method' => 'keyword_fallback',
                'keyword_scores' => array_slice($scores, 0, 3, true),
                'ai_provider' => 'none'
            ]
        ];
    }

    /**
     * Generate summary for article
     */
    public function generateSummary(string $content): string
    {
        $contentPreview = Str::limit(strip_tags($content), 2000);

        try {
            $response = Http::timeout(30)->post($this->apiUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => "Tóm tắt nội dung bài báo sau thành 2-3 câu ngắn gọn bằng tiếng Việt:\n\n{$contentPreview}"]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.5,
                    'maxOutputTokens' => 150,
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return $result['candidates'][0]['content']['parts'][0]['text'] ?? Str::limit(strip_tags($content), 200);
            }

            return Str::limit(strip_tags($content), 200);
        } catch (\Exception $e) {
            Log::error('Summary Generation Error: ' . $e->getMessage());
            return Str::limit(strip_tags($content), 200);
        }
    }
}
