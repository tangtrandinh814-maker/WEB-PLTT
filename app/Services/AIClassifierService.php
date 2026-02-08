<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AIClassifierService
{
    protected string $apiKey;
    protected string $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
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
        $contentPreview = Str::limit(strip_tags($content), 1000);

        return <<<PROMPT
Phân tích bài báo sau và trả lời theo định dạng JSON:

Tiêu đề: {$title}

Nội dung: {$contentPreview}

Các danh mục có sẵn: {$categoriesList}

Hãy trả về JSON với format sau (chỉ JSON, không có markdown):
{
    "category": "tên danh mục phù hợp nhất",
    "confidence": 0.95,
    "summary": "tóm tắt ngắn gọn 2-3 câu về bài báo",
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

            // Find matching category
            $categoryName = $data['category'] ?? null;
            $category = null;

            if ($categoryName) {
                $category = Category::where('name', 'LIKE', "%{$categoryName}%")->first();
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
     * Fallback classification when AI fails
     */
    private function fallbackClassification(string $title, string $content): array
    {
        // Simple keyword-based classification
        $text = mb_strtolower($title . ' ' . $content);

        $keywords = [
            'Thế Giới' => ['quốc tế', 'thế giới', 'nước ngoài', 'toàn cầu', 'mỹ', 'trung quốc', 'nga'],
            'Kinh Doanh' => ['kinh doanh', 'doanh nghiệp', 'công ty', 'thương mại', 'kinh tế', 'chứng khoán'],
            'Công Nghệ' => ['công nghệ', 'tech', 'ai', 'smartphone', 'laptop', 'phần mềm', 'apple', 'google'],
            'Thể Thao' => ['thể thao', 'bóng đá', 'olympic', 'vđv', 'hlv', 'giải đấu'],
            'Giải Trí' => ['giải trí', 'phim', 'ca sĩ', 'nghệ sĩ', 'showbiz', 'điện ảnh'],
            'Sức Khỏe' => ['sức khỏe', 'y tế', 'bệnh', 'thuốc', 'bác sĩ', 'bệnh viện'],
            'Thời Sự' => ['chính trị', 'chính phủ', 'quốc hội', 'luật', 'chính sách'],
        ];

        $maxScore = 0;
        $bestCategory = null;

        foreach ($keywords as $categoryName => $words) {
            $score = 0;
            foreach ($words as $word) {
                $score += mb_substr_count($text, $word);
            }

            if ($score > $maxScore) {
                $maxScore = $score;
                $bestCategory = $categoryName;
            }
        }

        $category = $bestCategory ? Category::where('name', $bestCategory)->first() : null;

        if (!$category) {
            $category = Category::first();
        }

        return [
            'category_id' => $category?->id,
            'confidence_score' => $maxScore > 0 ? min(0.7, $maxScore * 0.1) : 0.3,
            'summary' => Str::limit(strip_tags($content), 200),
            'tags' => [],
            'metadata' => [
                'sentiment' => 'neutral',
                'classification_method' => 'keyword_fallback',
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
