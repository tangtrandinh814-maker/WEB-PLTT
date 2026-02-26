<?php

/**
 * Re-crawl articles with improved classification
 */
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Source;
use App\Services\NewsCrawlerService;

// Reset last_crawled_at so sources will be crawled
Source::query()->update(['last_crawled_at' => null]);
echo "Reset all sources last_crawled_at\n";

// Crawl - resolve from container to inject dependencies
$crawler = app(NewsCrawlerService::class);
echo "Starting crawl...\n\n";

$results = $crawler->crawlAll();

echo "\n=== Crawl completed. Total processed: {$results} ===\n";

// Show classification results
echo "\n=== Category Distribution ===\n";
$categories = \App\Models\Category::withCount('articles')->orderByDesc('articles_count')->get();
foreach ($categories as $cat) {
    echo sprintf("  %-15s: %d articles\n", $cat->name, $cat->articles_count);
}

// Show sample articles per category
echo "\n=== Sample Articles ===\n";
foreach ($categories as $cat) {
    if ($cat->articles_count > 0) {
        echo "\n[{$cat->name}]\n";
        $articles = \App\Models\Article::where('category_id', $cat->id)->take(3)->get();
        foreach ($articles as $art) {
            $method = 'unknown';
            if ($art->metadata) {
                $meta = is_string($art->metadata) ? json_decode($art->metadata, true) : $art->metadata;
                $method = $meta['classification_method'] ?? $meta['ai_provider'] ?? 'unknown';
            }
            echo "  - [{$method}] {$art->title}\n";
        }
    }
}

echo "\nDone!\n";
