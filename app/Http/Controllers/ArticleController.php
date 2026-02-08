<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ArticleController extends Controller
{
    /**
     * Display homepage with latest articles
     */
    public function index(): View
    {
        $featuredArticles = Article::published()
            ->featured()
            ->with(['category', 'source'])
            ->latest('published_at')
            ->take(5)
            ->get();

        $latestArticles = Article::published()
            ->with(['category', 'source'])
            ->latest('published_at')
            ->paginate(12);

        $popularArticles = Article::published()
            ->with(['category', 'source'])
            ->popular(5)
            ->get();

        $categories = Category::active()
            ->ordered()
            ->withCount(['publishedArticles'])
            ->get();

        return view('articles.index', compact(
            'featuredArticles',
            'latestArticles',
            'popularArticles',
            'categories'
        ));
    }

    /**
     * Show single article
     */
    public function show(Article $article): View
    {
        // Increment views
        $article->incrementViews(
            request()->ip(),
            request()->userAgent()
        );

        // Load relationships
        $article->load(['category', 'source']);

        // Get related articles
        $relatedArticles = Article::published()
            ->where('id', '!=', $article->id)
            ->where('category_id', $article->category_id)
            ->with(['category', 'source'])
            ->latest('published_at')
            ->take(4)
            ->get();

        return view('articles.show', compact('article', 'relatedArticles'));
    }

    /**
     * Show articles by category
     */
    public function category(Category $category): View
    {
        $articles = $category->publishedArticles()
            ->with(['source'])
            ->latest('published_at')
            ->paginate(15);

        $categories = Category::active()
            ->ordered()
            ->withCount(['publishedArticles'])
            ->get();

        return view('articles.category', compact('category', 'articles', 'categories'));
    }

    /**
     * Search articles
     */
    public function search(Request $request): View
    {
        $query = $request->input('q', '');

        $articles = Article::published()
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                    ->orWhere('content', 'LIKE', "%{$query}%")
                    ->orWhere('summary', 'LIKE', "%{$query}%");
            })
            ->with(['category', 'source'])
            ->latest('published_at')
            ->paginate(15);

        return view('articles.search', compact('articles', 'query'));
    }
}
