<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Source;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;
use SimpleXMLElement;

class NewsCrawlerService
{
    protected Client $client;
    protected AIClassifierService $aiClassifier;

    public function __construct(AIClassifierService $aiClassifier)
    {
        $this->client = new Client([
            'timeout' => 30,
            'verify' => false,
        ]);
        $this->aiClassifier = $aiClassifier;
    }

    /**
     * Crawl all active sources
     */
    public function crawlAll(): int
    {
        $sources = Source::active()->get();
        $totalArticles = 0;

        foreach ($sources as $source) {
            if ($source->needsCrawling()) {
                $count = $this->crawlSource($source);
                $totalArticles += $count;
                Log::info("Crawled {$count} articles from {$source->name}");
            }
        }

        return $totalArticles;
    }

    /**
     * Crawl specific source
     */
    public function crawlSource(Source $source): int
    {
        try {
            if ($source->rss_url) {
                return $this->crawlRSS($source);
            } else {
                return $this->crawlWebsite($source);
            }
        } catch (\Exception $e) {
            Log::error("Error crawling {$source->name}: " . $e->getMessage());
            return 0;
        } finally {
            $source->markAsCrawled();
        }
    }

    /**
     * Crawl RSS feed
     */
    private function crawlRSS(Source $source): int
    {
        try {
            $response = $this->client->get($source->rss_url);
            $xmlContent = $response->getBody()->getContents();

            $xml = simplexml_load_string($xmlContent);

            if (!$xml) {
                throw new \Exception('Invalid RSS feed');
            }

            $count = 0;
            $items = $xml->channel->item ?? $xml->entry ?? [];

            foreach ($items as $item) {
                if ($this->saveArticleFromRSS($item, $source)) {
                    $count++;
                }

                // Limit to 10 articles per crawl
                if ($count >= 10) {
                    break;
                }
            }

            return $count;

        } catch (\Exception $e) {
            Log::error("RSS Crawl Error for {$source->name}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Save article from RSS item
     */
    private function saveArticleFromRSS(SimpleXMLElement $item, Source $source): bool
    {
        try {
            // Extract data from RSS item
            $title = (string) ($item->title ?? '');
            $link = (string) ($item->link ?? '');
            $description = (string) ($item->description ?? $item->summary ?? '');
            $pubDate = (string) ($item->pubDate ?? $item->published ?? '');

            // Clean HTML from description
            $description = strip_tags($description);

            if (empty($title) || empty($link)) {
                return false;
            }

            // Check if article already exists
            if (Article::where('original_url', $link)->exists()) {
                return false;
            }

            // Fetch full article content
            $fullContent = $this->fetchArticleContent($link);
            $content = $fullContent ?: $description;

            // Use AI to classify
            $classification = $this->aiClassifier->classifyArticle($title, $content);

            // Parse published date
            $publishedAt = $pubDate ? Carbon::parse($pubDate) : now();

            // Create article
            Article::create([
                'source_id' => $source->id,
                'category_id' => $classification['category_id'],
                'title' => $title,
                'content' => $content,
                'summary' => $classification['summary'],
                'original_url' => $link,
                'published_at' => $publishedAt,
                'ai_confidence_score' => $classification['confidence_score'],
                'ai_metadata' => $classification['metadata'],
                'tags' => $classification['tags'],
                'is_published' => $classification['confidence_score'] > 0.6,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error("Error saving RSS article: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch full article content from URL
     */
    private function fetchArticleContent(string $url): ?string
    {
        try {
            $response = $this->client->get($url);
            $html = $response->getBody()->getContents();

            // Simple content extraction
            $dom = new DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

            // Try to find article content
            $xpath = new DOMXPath($dom);

            // Common article selectors
            $selectors = [
                "//article",
                "//*[contains(@class, 'article-content')]",
                "//*[contains(@class, 'post-content')]",
                "//*[contains(@class, 'entry-content')]",
                "//div[contains(@class, 'content')]",
            ];

            foreach ($selectors as $selector) {
                $nodes = $xpath->query($selector);
                if ($nodes && $nodes->length > 0) {
                    $content = '';
                    foreach ($nodes as $node) {
                        $content .= $dom->saveHTML($node);
                    }

                    // Clean up content
                    $content = strip_tags($content, '<p><br><strong><em><ul><ol><li>');
                    return $content;
                }
            }

            return null;

        } catch (\Exception $e) {
            Log::error("Error fetching article content: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Crawl website (for sources without RSS)
     */
    private function crawlWebsite(Source $source): int
    {
        // This is a placeholder - you would need to implement
        // specific scraping logic for each website
        Log::info("Website crawling not implemented for {$source->name}");
        return 0;
    }
}
