<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$request = \Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

use App\Models\Article;

// Aggressive content cleaning function
function cleanArticleContent($content)
{
    // Remove everything after "//Chèn ads"
    if (strpos($content, '//') !== false) {
        $content = preg_replace('/\/\/.*$/ms', '', $content);
    }

    // Remove all script-like content
    $content = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $content);
    $content = preg_replace('/runinit.*?push\(.*?\);/is', '', $content);
    $content = preg_replace('/window\..*?;/is', '', $content);
    $content = preg_replace('/var\s+\w+\s*=.*?;/is', '', $content);
    $content = preg_replace('/function\s+\w+\s*\(.*?\)\s*\{.*?\}/is', '', $content);
    $content = preg_replace('/if\s*\(.*?\)\s*\{.*?\}/is', '', $content);
    $content = preg_replace('/for\s*\(.*?\)\s*\{.*?\}/is', '', $content);

    // Remove HTML comments
    $content = preg_replace('/<!--.*?-->/is', '', $content);

    // Remove style tags
    $content = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $content);
    $content = preg_replace('/\.[\w\-]+\s*\{.*?\}/is', '', $content);

    // Remove Taboola/ads content
    $content = preg_replace('/.*?taboola.*?;/is', '', $content);
    $content = preg_replace('/.*?arfAsync.*?;/is', '', $content);
    $content = preg_replace('/.*?(quảng cáo|ads|Quảng cáo).*?/is', '', $content);

    // Remove "Tin liên quan" and related sections
    $content = preg_replace('/Tin\s+liên\s+quan.*?/is', '', $content);
    $content = preg_replace('/Chia\s+sẻ.*?/is', '', $content);
    $content = preg_replace('/Gửi\s+bình\s+luận.*?/is', '', $content);

    // Remove empty lines and excessive whitespace
    $content = preg_replace('/\n\s*\n\s*\n/m', "\n\n", $content);
    $content = preg_replace('/^\s+/m', '', $content);
    $content = preg_replace('/\s+$/m', '', $content);
    $content = trim($content);

    return $content;
}

// Get all articles and clean them
$articles = Article::all();
$updated = 0;

echo "🔍 Checking " . count($articles) . " articles...\n\n";

foreach ($articles as $article) {
    $original = $article->content;
    $cleaned = cleanArticleContent($original);

    // Only update if content changed significantly
    if (
        strlen($cleaned) < strlen($original) * 0.8 ||
        strpos($original, 'runinit') !== false ||
        strpos($original, 'window.') !== false ||
        strpos($original, 'function ') !== false
    ) {

        $article->update(['content' => $cleaned]);
        $updated++;

        echo "✅ Cleaned: {$article->title}\n";
        echo "   Before: " . strlen($original) . " chars → After: " . strlen($cleaned) . " chars\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "✨ Updated {$updated} articles\n";
echo str_repeat("=", 60) . "\n";
