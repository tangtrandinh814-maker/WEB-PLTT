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

            // Extract image from RSS item first
            $imageUrl = $this->extractImageFromRSS($item, $description);

            // Clean HTML from description
            $description = strip_tags($description);

            if (empty($title) || empty($link)) {
                return false;
            }

            // Check if article already exists
            if (Article::where('original_url', $link)->exists()) {
                return false;
            }

            // Fetch full article content and page image
            $fetchResult = $this->fetchArticleContent($link);
            $content = $fetchResult['content'] ?: $description;

            // Use page image if RSS didn't provide one
            if (empty($imageUrl) && !empty($fetchResult['image'])) {
                $imageUrl = $fetchResult['image'];
            }

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
                'image_url' => $imageUrl,
                'original_url' => $link,
                'published_at' => $publishedAt,
                'ai_confidence_score' => $classification['confidence_score'],
                'ai_metadata' => $classification['metadata'],
                'tags' => $classification['tags'],
                'is_published' => $classification['confidence_score'] > 0.6,
                'is_featured' => !empty($imageUrl) && $classification['confidence_score'] > 0.7,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Error saving RSS article: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Extract image URL from RSS item
     */
    private function extractImageFromRSS(SimpleXMLElement $item, string $description): ?string
    {
        // 1. Check <enclosure> tag (common in RSS 2.0)
        if (isset($item->enclosure)) {
            $type = (string) $item->enclosure['type'];
            $url = (string) $item->enclosure['url'];
            if (!empty($url) && (empty($type) || str_starts_with($type, 'image/'))) {
                return $url;
            }
        }

        // 2. Check <media:content> or <media:thumbnail> (Media RSS namespace)
        $namespaces = $item->getNamespaces(true);
        if (isset($namespaces['media'])) {
            $media = $item->children($namespaces['media']);
            if (isset($media->content)) {
                $url = (string) $media->content->attributes()['url'];
                if (!empty($url)) {
                    return $url;
                }
            }
            if (isset($media->thumbnail)) {
                $url = (string) $media->thumbnail->attributes()['url'];
                if (!empty($url)) {
                    return $url;
                }
            }
        }

        // 3. Check for image in <description> HTML
        if (!empty($description) && preg_match('/<img[^>]+src=["\']([^"\'>]+)["\']/i', $description, $matches)) {
            return $matches[1];
        }

        // 4. Check <image> child element
        if (isset($item->image)) {
            $url = (string) $item->image;
            if (!empty($url)) {
                return $url;
            }
        }

        return null;
    }

    /**
     * Fetch full article content and featured image from URL
     * Returns ['content' => string|null, 'image' => string|null]
     */
    private function fetchArticleContent(string $url): array
    {
        $result = ['content' => null, 'image' => null];

        try {
            $response = $this->client->get($url);
            $html = $response->getBody()->getContents();

            // Simple content extraction
            $dom = new DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new DOMXPath($dom);

            // --- Extract featured image ---
            $result['image'] = $this->extractImageFromPage($xpath, $dom, $url);

            // --- Remove unwanted nodes from DOM before extracting content ---
            $this->removeUnwantedNodes($xpath);

            // --- Extract article content ---
            // Common article selectors
            $selectors = [
                "//article",
                "//*[contains(@class, 'article-content')]",
                "//*[contains(@class, 'article-body')]",
                "//*[contains(@class, 'post-content')]",
                "//*[contains(@class, 'entry-content')]",
                "//*[contains(@class, 'detail-content')]",
                "//*[contains(@class, 'fck_detail')]",
                "//div[contains(@class, 'content')]",
            ];

            foreach ($selectors as $selector) {
                $nodes = $xpath->query($selector);
                if ($nodes && $nodes->length > 0) {
                    $content = '';
                    foreach ($nodes as $node) {
                        $content .= $dom->saveHTML($node);
                    }

                    // Clean up content - keep <img> tags for inline images
                    $content = strip_tags($content, '<p><br><strong><em><b><i><ul><ol><li><img><figure><figcaption><h2><h3><h4><blockquote>');
                    $result['content'] = $content;
                    return $result;
                }
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("Error fetching article content: " . $e->getMessage());
            return $result;
        }
    }

    /**
     * Extract the featured/thumbnail image from a web page
     */
    private function extractImageFromPage(DOMXPath $xpath, DOMDocument $dom, string $url): ?string
    {
        // 1. Open Graph image (og:image) — most reliable for featured image
        $ogImage = $xpath->query("//meta[@property='og:image']/@content");
        if ($ogImage && $ogImage->length > 0) {
            $imageUrl = $ogImage->item(0)->nodeValue;
            if (!empty($imageUrl)) {
                return $this->resolveUrl($imageUrl, $url);
            }
        }

        // 2. Twitter card image
        $twitterImage = $xpath->query("//meta[@name='twitter:image']/@content");
        if ($twitterImage && $twitterImage->length > 0) {
            $imageUrl = $twitterImage->item(0)->nodeValue;
            if (!empty($imageUrl)) {
                return $this->resolveUrl($imageUrl, $url);
            }
        }

        // 3. Schema.org image (JSON-LD)
        $scripts = $xpath->query("//script[@type='application/ld+json']");
        if ($scripts) {
            foreach ($scripts as $script) {
                $json = json_decode($script->textContent, true);
                if ($json) {
                    $schemaImage = $json['image'] ?? $json['thumbnailUrl'] ?? null;
                    if (is_array($schemaImage)) {
                        $schemaImage = $schemaImage['url'] ?? $schemaImage[0] ?? null;
                    }
                    if (!empty($schemaImage)) {
                        return $this->resolveUrl($schemaImage, $url);
                    }
                }
            }
        }

        // 4. First large image inside article content
        $articleSelectors = [
            "//article//img",
            "//*[contains(@class, 'article')]//img",
            "//*[contains(@class, 'content')]//img",
            "//*[contains(@class, 'detail')]//img",
        ];

        foreach ($articleSelectors as $selector) {
            $images = $xpath->query($selector);
            if ($images && $images->length > 0) {
                foreach ($images as $img) {
                    $src = $img->getAttribute('src') ?: $img->getAttribute('data-src');
                    if (!empty($src) && !$this->isIconOrTracker($src)) {
                        return $this->resolveUrl($src, $url);
                    }
                }
            }
        }

        return null;
    }

    /**
     * Resolve relative URL to absolute
     */
    private function resolveUrl(string $imageUrl, string $pageUrl): string
    {
        // Already absolute
        if (str_starts_with($imageUrl, 'http://') || str_starts_with($imageUrl, 'https://')) {
            return $imageUrl;
        }

        // Protocol-relative
        if (str_starts_with($imageUrl, '//')) {
            return 'https:' . $imageUrl;
        }

        // Relative URL — resolve against page URL
        $parsed = parse_url($pageUrl);
        $base = ($parsed['scheme'] ?? 'https') . '://' . ($parsed['host'] ?? '');

        if (str_starts_with($imageUrl, '/')) {
            return $base . $imageUrl;
        }

        // Relative path
        $basePath = isset($parsed['path']) ? dirname($parsed['path']) : '';
        return $base . $basePath . '/' . $imageUrl;
    }

    /**
     * Check if image URL is likely an icon, tracker pixel, or ad
     */
    private function isIconOrTracker(string $url): bool
    {
        $patterns = [
            '/favicon/',
            '/icon/',
            '/logo/',
            '/pixel/',
            '/tracker/',
            '/beacon/',
            '/ads/',
            '/banner/',
            '1x1',
            'spacer',
            'blank.gif',
            'transparent.png',
        ];

        $lower = strtolower($url);
        foreach ($patterns as $pattern) {
            if (str_contains($lower, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove script, style, and other unwanted nodes from DOM
     * This prevents JavaScript code from leaking into article text
     */
    private function removeUnwantedNodes(DOMXPath $xpath): void
    {
        $unwantedSelectors = [
            '//script',
            '//style',
            '//noscript',
            '//iframe',
            '//form',
            '//input',
            '//button',
            '//select',
            '//textarea',
            '//nav',
            '//footer',
            '//header',
            "//*[contains(@class, 'ads')]",
            "//*[contains(@class, 'advertisement')]",
            "//*[contains(@class, 'social-share')]",
            "//*[contains(@class, 'related-news')]",
            "//*[contains(@class, 'comment')]",
            "//*[contains(@class, 'sidebar')]",
            "//*[contains(@class, 'widget')]",
            "//*[contains(@class, 'popup')]",
            "//*[contains(@class, 'modal')]",
            "//*[contains(@id, 'ads')]",
            "//*[contains(@id, 'comment')]",
        ];

        foreach ($unwantedSelectors as $selector) {
            $nodes = $xpath->query($selector);
            if ($nodes) {
                // Collect nodes first to avoid modifying DOM while iterating
                $toRemove = [];
                foreach ($nodes as $node) {
                    $toRemove[] = $node;
                }
                foreach ($toRemove as $node) {
                    if ($node->parentNode) {
                        $node->parentNode->removeChild($node);
                    }
                }
            }
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
