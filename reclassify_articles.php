<?php

/**
 * Reclassify all articles using the improved AI + fallback system
 * Run: php reclassify_articles.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Article;
use App\Models\Category;
use App\Services\AIClassifierService;

$classifier = new AIClassifierService();
$articles = Article::all();

echo "=== Reclassifying " . $articles->count() . " articles ===\n\n";

$categoryStats = [];
$success = 0;
$failed = 0;

foreach ($articles as $article) {
    $oldCategory = Category::find($article->category_id);
    $oldCategoryName = $oldCategory ? $oldCategory->name : 'N/A';

    try {
        $result = $classifier->classifyArticle($article->title, $article->content ?? '');

        if ($result && isset($result['category_id']) && $result['category_id']) {
            $newCategory = Category::find($result['category_id']);
            $newCategoryName = $newCategory ? $newCategory->name : 'N/A';
            $method = $result['metadata']['classification_method'] ?? $result['metadata']['ai_provider'] ?? 'unknown';
            $confidence = $result['confidence_score'] ?? 0;

            $article->update([
                'category_id' => $result['category_id'],
                'ai_confidence_score' => $confidence,
                'ai_summary' => $result['summary'] ?? $article->ai_summary,
            ]);

            $changed = ($oldCategoryName !== $newCategoryName) ? ' [CHANGED]' : '';
            echo sprintf(
                "%-3d | %-12s -> %-12s | %.2f | %-10s | %s%s\n",
                $article->id,
                $oldCategoryName,
                $newCategoryName,
                $confidence,
                $method,
                mb_substr($article->title, 0, 50),
                $changed
            );

            $categoryStats[$newCategoryName] = ($categoryStats[$newCategoryName] ?? 0) + 1;
            $success++;
        } else {
            echo "FAILED: {$article->title}\n";
            $failed++;
        }
    } catch (\Exception $e) {
        echo "ERROR: {$article->title} - {$e->getMessage()}\n";
        $failed++;
    }

    // Small delay to avoid rate limiting on Gemini API
    usleep(500000); // 0.5 second
}

echo "\n=== Results ===\n";
echo "Success: {$success}, Failed: {$failed}\n\n";

echo "Category Distribution:\n";
arsort($categoryStats);
foreach ($categoryStats as $name => $count) {
    echo sprintf("  %-15s: %d articles\n", $name, $count);
}
echo "\nDone!\n";
