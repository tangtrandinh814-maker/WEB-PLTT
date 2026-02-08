<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$request = \Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

use App\Helpers\ArticleHelper;

$article = \App\Models\Article::where('slug', 'trao-qua-tet-binh-ngo-da-nang-2026')->first();
if ($article) {
    echo "✅ Original content length: " . strlen($article->content) . " chars\n";

    $sanitized = ArticleHelper::sanitizeContent($article->content);
    echo "📊 Sanitized content length: " . strlen($sanitized) . " chars\n";

    $formatted = ArticleHelper::formatContent($article->content);
    echo "📄 Formatted content length: " . strlen($formatted) . " chars\n";

    echo "\n✅ Sanitization working correctly!\n";
    echo "\nFirst 200 chars of formatted content:\n";
    echo strip_tags(substr($formatted, 0, 200)) . "...\n";
} else {
    echo "❌ Article not found\n";
}
