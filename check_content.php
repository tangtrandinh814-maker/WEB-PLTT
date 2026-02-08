<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$request = \Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

$article = \App\Models\Article::where('slug', 'trao-qua-tet-binh-ngo-da-nang-2026')->first();
if ($article) {
    echo "✅ Title: " . $article->title . "\n";
    echo "📊 Content length: " . strlen($article->content) . " chars\n";
    echo "🔍 Has <script tags: " . (strpos($article->content, '<script') !== false ? 'YES ❌' : 'NO ✅') . "\n";
    echo "🔍 Has JavaScript code: " . (strpos($article->content, 'runinit') !== false ? 'YES ❌' : 'NO ✅') . "\n";
    echo "🔍 Has HTML comments: " . (strpos($article->content, '//') !== false ? 'YES ❌' : 'NO ✅') . "\n";
    echo "\n--- First 300 chars of content ---\n";
    echo substr($article->content, 0, 300) . "...\n";
} else {
    echo "❌ Article not found\n";
}
